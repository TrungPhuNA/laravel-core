Hiện tại đây là body setup của mình
{
  "request_name": "required|string|max:255",
  "problem": "nullable|string",
  "product_link": "nullable|url",
  "current_sales_volume": "nullable|numeric",
  "unique_selling_point": "nullable|string",
  "customer_segment": "nullable|string",
  "target_market": "nullable|string",
  "promotion_program": "nullable|string",
  "after_sales_policy": "nullable|string",
  "sales_channels": "nullable|string",
  "conversion_rate": "nullable|numeric|min:0|max:100",
  "expected_goal": "nullable|string",
  "selected_solution": "nullable|string",
  "expected_start_time": "nullable|date",
  "expected_end_time": "nullable|date",
  "budget": "nullable|numeric|min:0",
  "user_flow": "nullable|string",
  "contact_name": "nullable|string|max:255",
  "contact_email": "nullable|email",
  "contact_phone": "nullable|string|max:20",
  "company_name": "nullable|string|max:255",
  "campaign_source": "nullable|string",
  "campaign_medium": "nullable|string",
  "campaign_name": "nullable|string",
  "campaign_content": "nullable|string",
  "note": "nullable|string",
  "products": "nullable|array",
  "products.*.name": "nullable|string|max:255",
  "products.*.price": "nullable|numeric|min:0",
  "total_amount": "nullable|numeric|min:0",
  "payment_account": "nullable|string",
  "contract": "nullable|string",
  "order_id": "nullable|string"
}

NHưng hiện tại bên bắn dữ liệu đang không bắn được theo format trên
body họ có thể bắm

{
    "id": 675,
    "parent_id": 0,
    "status": "on-hold",
    "currency": "VND",
    "version": "10.6.2",
    "prices_include_tax": false,
    "date_created": "2026-04-22T14:08:40",
    "date_modified": "2026-04-22T14:08:40",
    "discount_total": "0",
    "discount_tax": "0",
    "shipping_total": "0",
    "shipping_tax": "0",
    "cart_tax": "0",
    "total": "17000000",
    "total_tax": "0",
    "customer_id": 2,
    "order_key": "wc_order_qonqBfVqNGuHT",
    "billing": {
        "first_name": "",
        "last_name": "Đức Thắng 1",
        "company": "Công ty TNHH Interspace 1",
        "address_1": "23456u",
        "address_2": "",
        "city": "Phường Bắc Giang",
        "state": "Bắc Ninh",
        "postcode": "",
        "country": "",
        "email": "test@gmail.com",
        "phone": "0987654321"
    },
    "shipping": {
        "first_name": "",
        "last_name": "",
        "company": "",
        "address_1": "",
        "address_2": "",
        "city": "",
        "state": "",
        "postcode": "",
        "country": "",
        "phone": ""
    },
    "payment_method": "sepay",
    "payment_method_title": "Chuyển khoản ngân hàng (Quét mã QR)",
    "transaction_id": "",
    "customer_ip_address": "27.72.98.188",
    "customer_user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36",
    "created_via": "checkout",
    "customer_note": "",
    "date_completed": null,
    "date_paid": null,
    "cart_hash": "a5b41828fee9662559089a3a65419e18",
    "number": "675",
    "meta_data": [
        {
            "id": 283,
            "key": "_billing_representative",
            "value": "Do Huu Hung"
        },
        {
            "id": 284,
            "key": "_billing_tax_code",
            "value": "12345678"
        },
        {
            "id": 298,
            "key": "_debug_log_source_pending_deletion",
            "value": "place-order-debug-f1074f5d"
        },
        {
            "id": 295,
            "key": "_wc_order_attribution_device_type",
            "value": "Desktop"
        },
        {
            "id": 293,
            "key": "_wc_order_attribution_session_count",
            "value": "1"
        },
        {
            "id": 290,
            "key": "_wc_order_attribution_session_entry",
            "value": "https://merchant.accesstrade.me/"
        },
        {
            "id": 292,
            "key": "_wc_order_attribution_session_pages",
            "value": "15"
        },
        {
            "id": 291,
            "key": "_wc_order_attribution_session_start_time",
            "value": "2026-04-22 06:55:26"
        },
        {
            "id": 288,
            "key": "_wc_order_attribution_source_type",
            "value": "typein"
        },
        {
            "id": 294,
            "key": "_wc_order_attribution_user_agent",
            "value": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36"
        },
        {
            "id": 289,
            "key": "_wc_order_attribution_utm_source",
            "value": "(direct)"
        },
        {
            "id": 285,
            "key": "is_vat_exempt",
            "value": "no"
        }
    ],
    "line_items": [
        {
            "id": 31,
            "name": "Premium Daily Livestream",
            "product_id": 434,
            "variation_id": 457,
            "quantity": 1,
            "tax_class": "",
            "subtotal": "17000000",
            "subtotal_tax": "0",
            "total": "17000000",
            "total_tax": "0",
            "taxes": [],
            "meta_data": [
                {
                    "id": 324,
                    "key": "pa_loai",
                    "value": "premium-daily-livestream",
                    "display_key": "Loại",
                    "display_value": "Premium Daily Livestream"
                },
                {
                    "id": 325,
                    "key": "pa_so-phien",
                    "value": "combo-20-phien",
                    "display_key": "Số phiên",
                    "display_value": "Combo 20 phiên"
                }
            ],
            "sku": "",
            "global_unique_id": "",
            "price": 17000000,
            "image": {
                "id": 471,
                "src": "https://merchant.accesstrade.me/wp-content/uploads/2026/04/daily-livestream.png"
            },
            "parent_name": "Premium Daily Livestream"
        }
    ],
    "tax_lines": [],
    "shipping_lines": [],
    "fee_lines": [],
    "coupon_lines": [],
    "refunds": [],
    "payment_url": "https://merchant.accesstrade.me/checkout/order-pay/675/?pay_for_order=true&key=wc_order_qonqBfVqNGuHT",
    "is_editable": true,
    "needs_payment": false,
    "needs_processing": true,
    "date_created_gmt": "2026-04-22T07:08:40",
    "date_modified_gmt": "2026-04-22T07:08:40",
    "date_completed_gmt": null,
    "date_paid_gmt": null,
    "currency_symbol": "VND",
    "_links": {
        "self": [
            {
                "href": "https://merchant.accesstrade.me/wp-json/wc/v3/orders/675",
                "targetHints": {
                    "allow": [
                        "GET",
                        "POST",
                        "PUT",
                        "PATCH",
                        "DELETE"
                    ]
                }
            }
        ],
        "collection": [
            {
                "href": "https://merchant.accesstrade.me/wp-json/wc/v3/orders"
            }
        ],
        "email_templates": [
            {
                "embeddable": true,
                "href": "https://merchant.accesstrade.me/wp-json/wc/v3/orders/675/actions/email_templates"
            }
        ],
        "customer": [
            {
                "href": "https://merchant.accesstrade.me/wp-json/wc/v3/customers/2"
            }
        ]
    }
}



