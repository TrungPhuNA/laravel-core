import type { ApiResponseSuccess } from "@shared/http/types";
import { api } from "../../../shared/lib/api";

export type ApiUser = {
    id: number;
    name: string | null;
    email: string | null;
    phone: string | null;
    user_type: string;
};

export async function login(input: { email: string; password: string }): Promise<{ user: ApiUser; token: string }> {
    const res = await api.post<ApiResponseSuccess<{ user: ApiUser; token: string }>>("/auth/login", input);
    if (res.data.status !== "success") throw res.data;
    return res.data.data;
}

export async function register(input: {
    name: string;
    email: string;
    phone?: string | null;
    password: string;
    password_confirmation: string;
}): Promise<{ user: ApiUser; token: string }> {
    const res = await api.post<ApiResponseSuccess<{ user: ApiUser; token: string }>>("/auth/register", input);
    if (res.data.status !== "success") throw res.data;
    return res.data.data;
}

