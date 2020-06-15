/**
 * Interface for API model deserialization.
 */
export interface ApiModel<T> {
  new (...args: any[]): T;
  deserialize(input: any): T;
  deserializeArray(input: any[]): T[];
}
