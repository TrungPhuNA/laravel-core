import axios, { AxiosError } from "axios";
import type { ApiResponse } from "./types";

type CreateApiClientOptions = {
  baseURL: string;
  getToken: () => string;
  getLocale: () => string;
};

export function createApiClient(opts: CreateApiClientOptions) {
  const baseURL = String(opts.baseURL ?? "").replace(/\/+$/, "");

  const client = axios.create({
    baseURL,
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    timeout: 20_000,
  });

  client.interceptors.request.use((config) => {
    const token = opts.getToken().trim();
    const locale = opts.getLocale().trim();

    if (token !== "") {
      config.headers = config.headers ?? {};
      config.headers.Authorization = `Bearer ${token}`;
    }

    if (locale !== "") {
      config.headers = config.headers ?? {};
      config.headers["X-Locale"] = locale;
    }

    return config;
  });

  client.interceptors.response.use(
    (res) => res,
    (error: AxiosError<ApiResponse<unknown>>) => {
      // Chuẩn hoá lỗi để UI dễ hiển thị theo JSend.
      if (error.response?.data) {
        throw error.response.data;
      }
      throw error;
    }
  );

  return client;
}

