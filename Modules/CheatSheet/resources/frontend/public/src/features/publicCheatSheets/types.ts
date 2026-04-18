export type PublicTopicItem = {
  slug: string;
  name: string;
  count: number;
};

export type PublicCheatSheetTagItem = {
  id: number;
  name: string;
  slug: string;
};

export type PublicCheatSheetListItem = {
  id: number;
  title: string;
  excerpt: string;
  published_at: string | null;
  updated_at: string;
  tags: PublicCheatSheetTagItem[];
  author: { id: number; name: string | null } | null;
};

export type PublicCheatSheet = {
  id: number;
  title: string;
  body: string;
  published_at: string | null;
  created_at: string;
  updated_at: string;
  tags: PublicCheatSheetTagItem[];
  author: { id: number; name: string | null } | null;
};

