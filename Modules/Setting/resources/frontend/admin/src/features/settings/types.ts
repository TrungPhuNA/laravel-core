export type SettingItem = {
  key: string;
  value: unknown;
  group: string | null;
  is_public: boolean;
  description: string | null;
  updated_by: number | null;
  updated_at: string | null;
};

