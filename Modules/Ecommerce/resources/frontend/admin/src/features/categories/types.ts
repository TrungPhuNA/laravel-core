export type Category = {
  id: number;
  parent_id: number | null;
  name: string;
  slug: string;
  description: string | null;
  position: number;
  is_active: boolean;
  created_at?: string | null;
  updated_at?: string | null;
};

