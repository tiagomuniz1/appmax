# Appmax - Technical Challenger

---

## Stack

- PHP 8.5+
- Laravel
- MySQL
- Docker / Docker Compose
- JWT Auth (tymon/jwt-auth)
- PHPUnit

---

## Requiriments

- Docker
- Docker Compose

---

## Setup

### 1 - Clone repository

```bash
git clone https://github.com/tiagomuniz1/appmax.git
cd appmax
```

### 2 - Run setup script to configure the project in docker
```bash
./setup.sh
```

## Setup test environment

> [!CAUTION]
>Obs: make sure you run the project setup first

```bash
./setup_test_env.sh
```


## Running project

```bash
docker compose exec app php artisan serve --host=0.0.0.0 --port=8000
```
This is going to lock your terminal with the API running and you can access the API with this URL: http://localhost:8000/

## Endpoints

### Auth

#### REGISTER

    POST /api/v1/auth/register | 201

Expected Payload:
```json
{
    "name": "Tiago Muniz",
    "email": "tiagomuniz1@gmail.com",
    "password": "123123123"
}
```

Expected Response:
```json
{
    "user": {
        "id": 1,
        "name": "Tiago Muniz",
        "email": "tiagomuniz1@gmail.com",
        "updated_at": "2025-12-24T16:08:07.000000Z",
        "created_at": "2025-12-24T16:08:07.000000Z"
    }
}
```

#### LOGIN
    POST /api/v1/auth/login | 200

Expected Payload:
```json
{
  "email": "tiagomuniz1@gmail.com",
  "password": "123123123"
}
```

Expected Response:
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL3YxL2F1dGgvbG9naW4iLCJpYXQiOjE3NjY1OTI0OTUsImV4cCI6MTc2NjU5NjA5NSwibmJmIjoxNzY2NTkyNDk1LCJqdGkiOiI3aURnZ3NLMDNoU3lCTDZ5Iiwic3ViIjoiMiIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.RDe-dDAs8XC0nlwBpDjsAVRmETEZIlJjACjfKu1hNAE",
    "token_type": "bearer",
    "expires_in": 3600
}
```


### Wallet


Expected Request Headers for all requests below:
```
Authorization: Bearer TOKEN_FROM_LOGIN_HERE
Accept: application/json
```

#### BALANCE
    POST /api/v1/wallet/balance | 200

Expected Response:
```json
{
    "balance": 0 // user wallter balance can change after some transactions
}
```

#### DEPOSIT
    POST /api/v1/wallet/deposit | 204

Expected Response: This endpoint has no responde body


#### WITHDRAW
    POST /api/v1/wallet/withdraw | 204

Expected Payload:
```json
{
    "amount": 100.00
}
```

Expected Response: This endpoint has no responde body

#### TRANSFER
    POST /api/v1/wallet/transfer | 204


Expected Payload:
```json
{
    "email": "recipient_transfer@example.com",
    "amount": 10.00
}
```

Expected Response: This endpoint has no responde body


#### TRANSACTIONS
    POST /api/v1/wallet/transactions?page=1&per_page=10&order=desc | 200

Expected Response: 

```json
{
    "data": [
        {
            "id": 3,
            "type": "transfer_out",
            "amount": 5,
            "balance_before": 10,
            "balance_after": 5,
            "created_at": "2025-12-24T18:44:31.000000Z"
        },
        {
            "id": 2,
            "type": "withdraw",
            "amount": 5,
            "balance_before": 15,
            "balance_after": 10,
            "created_at": "2025-12-24T18:44:17.000000Z"
        },
        {
            "id": 1,
            "type": "deposit",
            "amount": 15,
            "balance_before": 0,
            "balance_after": 15,
            "created_at": "2025-12-24T18:43:59.000000Z"
        }
    ],
    "meta": {
        "page": 1,
        "per_page": 10,
        "total": 3,
        "last_page": 1
    }
}
```

Obs: These payloads and responses are examples and not real data.

You can call all APIs using the [Postman Collection](./docs/Appmax.postman_collection.json).


## Testing

### Running tests

```bash
docker compose exec app php artisan test
```

### Running tests with coverage

```bash
docker compose exec app php artisan test --coverage-html=coverage
```

To see coverage report you can open the [coverage repost](./coverage/index.html) in browser.
