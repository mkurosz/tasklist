<?php

namespace Tasklist\Task\Controller;

use DateTimeImmutable;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tasklist\Common\Controller\ApiValidationErrorsControllerTrait;
use Tasklist\Task\Dto\BoardInputDto;
use Tasklist\Task\Entity\Board;
use Tasklist\Task\Repository\BoardRepository;
use Tasklist\Task\Service\BoardCrudManger;
use Tasklist\User\Entity\User;

/**
 * BoardsController.
 */
class BoardsController extends AbstractFOSRestController
{
    use ApiValidationErrorsControllerTrait;

    /**
     * Token storage.
     *
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Board repository.
     *
     * @var BoardRepository
     */
    private $boardRepository;

    /**
     * Board CRUD manger.
     *
     * @var BoardCrudManger
     */
    private $boardCrudManger;

    /**
     * Validator.
     *
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * BoardsController constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param BoardRepository $boardRepository
     * @param BoardCrudManger $boardCrudManger
     * @param ValidatorInterface $validator
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        BoardRepository $boardRepository,
        BoardCrudManger $boardCrudManger,
        ValidatorInterface $validator
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->boardRepository = $boardRepository;
        $this->boardCrudManger = $boardCrudManger;
        $this->validator = $validator;
    }

    /**
     * Get user boards.
     *
     * @return View
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns user boards.",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Board::class))
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function getBoardsAction(): View
    {
        try {
            return $this->view(
                    $this->boardRepository->findby([
                        'owner' => $this->tokenStorage->getToken()->getUser(),
                    ]),
                    Response::HTTP_OK
                );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Get user board for the given date.
     *
     * @param DateTimeImmutable $date
     *
     * @return View
     *
     * @OA\Parameter(
     *     name="date",
     *     in="path",
     *     type="string",
     *     description="Date of the board."
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns the user board for the given date.",
     *     @Model(type=Board::class)
     * )
     * @Security(name="Bearer")
     */
    public function getBoardAction(DateTimeImmutable $date): View
    {
        try {
            return $this->view(
                $this->boardRepository->findOneBy([
                    'owner' => $this->tokenStorage->getToken()->getUser(),
                    'date' => $date,
                ]),
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Create board.
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @RequestParam(
     *     name="date",
     *     requirements=@Constraints\Date,
     *     description="Date of the board."
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns newly created user board.",
     *     @Model(type=Board::class)
     * )
     */
    public function postBoardAction(ParamFetcher $paramFetcher): View
    {
        try {
            /* @var $user User */
            $user = $this->tokenStorage->getToken()->getUser();

            $newBoard = new Board(
                new DateTimeImmutable($paramFetcher->get('date')),
                $user
            );

            $validationErrors = $this->validator->validate($newBoard);

            if ($validationErrors->count()) {
                return $this->createValidationErrorsResponse($validationErrors);
            }

            return $this->view(
                $this->boardCrudManger->create($newBoard)
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Update board.
     *
     * @param Board $board
     * @param BoardInputDto $boardInput
     *
     * @return View
     *
     * @ParamConverter("board")
     * @ParamConverter("boardInput", converter="fos_rest.request_body")
     *
     * @OA\Parameter(
     *     name="board",
     *     in="path",
     *     type="integer",
     *     description="Board id."
     * )
     * @OA\Parameter(
     *     name="boardInput",
     *     in="body",
     *     description="Board details to update.",
     *     @Model(type=BoardInputDto::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns updated user board.",
     *     @Model(type=Board::class)
     * )
     */
    public function patchBoardAction(Board $board, BoardInputDto $boardInput): View
    {
        try {
            /* @var $user User */
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user !== $board->getOwner()) {
                throw new AccessDeniedHttpException('Update of other user\'s board is forbidden');
            }

            $validationErrors = $this->validator->validate($boardInput, null, ['Default', 'UpdateBoard']);

            if ($validationErrors->count()) {
                return $this->createValidationErrorsResponse($validationErrors);
            }

            return $this->view(
                $this->boardCrudManger->updateFromDto($board, $boardInput)
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Copy board.
     *
     * @param Board $board
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ParamConverter("board")
     *
     * @RequestParam(
     *     name="date",
     *     requirements=@Constraints\Date,
     *     description="Date of the board."
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns copied user board.",
     *     @Model(type=Board::class)
     * )
     */
    public function postBoardCopyAction(Board $board, ParamFetcher $paramFetcher): View
    {
        try {
            /* @var $user User */
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user !== $board->getOwner()) {
                throw new AccessDeniedHttpException('Copying of other user\'s board is forbidden');
            }

            $newBoard = $board->cloneEntity(new DateTimeImmutable($paramFetcher->get('date')));

            $validationErrors = $this->validator->validate($newBoard);

            if ($validationErrors->count()) {
                return $this->createValidationErrorsResponse($validationErrors);
            }

            return $this->view(
                $this->boardCrudManger->create($newBoard)
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }
}
