export type OrderItemInput = {
  product_id?: number | null;
  sku?: string | null;
  name?: string | null;
  quantity: number;
  unit_price: number;
};

export type Order = {
  id: number;
  code: string;
  customer_id: number | null;
  status: string;
  payment_status: string;
  fulfillment_status: string;
  subtotal: string;
  total: string;
  currency: string;
  customer_email: string | null;
  customer_phone: string | null;
  placed_at?: string | null;
  created_at?: string | null;
};

