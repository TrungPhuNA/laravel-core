import type { ApiResponseSuccess } from "@shared/http/types";
import { api } from "../../../shared/lib/api";

export type ProfileUser = {
    id: number;
    name: string | null;
    email: string | null;
    user_type: string;
    phone: string | null;
    avatar_url: string | null;
    date_of_birth: string | null;
    gender: string | null;
    address_line1: string | null;
    address_line2: string | null;
    ward: string | null;
    district: string | null;
    province: string | null;
    country: string | null;
    postal_code: string | null;
    company: string | null;
    job_title: string | null;
    timezone: string | null;
    locale: string | null;
    bio: string | null;
};

export async function me(): Promise<ProfileUser> {
    const res = await api.get<ApiResponseSuccess<{ user: ProfileUser }>>("/auth/me");
    if (res.data.status !== "success") throw res.data;
    return res.data.data.user;
}

export type UpdateProfileInput = Partial<Pick<
    ProfileUser,
    | "name"
    | "phone"
    | "avatar_url"
    | "date_of_birth"
    | "gender"
    | "address_line1"
    | "address_line2"
    | "ward"
    | "district"
    | "province"
    | "country"
    | "postal_code"
    | "company"
    | "job_title"
    | "timezone"
    | "locale"
    | "bio"
>>;

export async function updateProfile(input: UpdateProfileInput): Promise<ProfileUser> {
    const res = await api.put<ApiResponseSuccess<{ user: ProfileUser }>>("/auth/profile", input);
    if (res.data.status !== "success") throw res.data;
    return res.data.data.user;
}

