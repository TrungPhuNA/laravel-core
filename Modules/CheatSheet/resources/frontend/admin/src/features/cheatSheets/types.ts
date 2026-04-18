export type CheatSheetTagItem = {
  id: number;
  name: string;
  slug: string;
};

export type CheatSheetItem = {
  id: number;
  title: string;
  body: string;
  visibility: "private" | "unlisted" | "public";
  published_at: string | null;
  tags: CheatSheetTagItem[];
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
};

