import type { Category } from "../categories/types";

export type Product = {
  id: number;
  sku: string;
  name: string;
  slug: string;
  description: string | null;
  price: string;
  compare_at_price: string | null;
  currency: string;
  stock_qty: number;
  is_active: boolean;
  categories?: Category[];
};

