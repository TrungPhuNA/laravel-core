export type ApiStatus = "success" | "fail" | "error";

export type ApiMetaPagination = {
  page: number;
  per_page: number;
  total: number;
  last_page: number;
  from: number | null;
  to: number | null;
};

export type ApiResponseSuccess<T> = {
  status: "success";
  code: string;
  message: string;
  data: T;
  meta?: ApiMetaPagination | Record<string, unknown>;
  trace_id?: string;
};

export type ApiResponseFail<T = Record<string, unknown>> = {
  status: "fail";
  code: string;
  message: string;
  data: T;
  meta?: Record<string, unknown>;
  trace_id?: string;
};

export type ApiResponseError<T = Record<string, unknown>> = {
  status: "error";
  code?: string;
  message: string;
  data?: T;
  meta?: Record<string, unknown>;
  trace_id?: string;
};

export type ApiResponse<T> = ApiResponseSuccess<T> | ApiResponseFail | ApiResponseError;

