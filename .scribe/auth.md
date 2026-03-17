# Xác thực request

Để xác thực, gửi header **`Authorization`** với giá trị **`"Bearer {TOKEN}"`**.

Tất cả endpoint cần xác thực sẽ có nhãn `requires authentication` trong tài liệu bên dưới.

Lấy token qua `POST /api/v1/auth/login`, sau đó gửi header `Authorization: Bearer <token>`.
