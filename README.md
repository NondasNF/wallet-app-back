# Backend Laravel - Carteira Financeira

## 📌 Descrição
Este projeto é um backend desenvolvido em **Laravel** para gerenciamento de uma carteira financeira. Ele permite registro de usuários, autenticação, depósitos, transferências e consultas de saldo e transações e estorno de transações.

---

## 🚀 Tecnologias Utilizadas
- **PHP 8.3**
- **Laravel**
- **Laravel Sanctum** (Autenticação via Token)
- **MySQL** (Banco de Dados)
- **Docker** (Ambiente de desenvolvimento)
- **PHPUnit** (Testes automatizados)

---

## 📂 Estrutura do Projeto
```
backend/
│── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── WalletController.php
│   │   │   ├── TransactionController.php
│── database/
│   ├── factories/
│   ├── migrations/
│── tests/
│   ├── Feature/
│   ├── Unit/
│── .env
│── docker-compose.yml
│── README.md
```

---

## 🔧 Instalação e Configuração
### **1. Clone o repositório**
```bash
git clone https://github.com/NondasNF/wallet-app-back.git
cd wallet-app-back
```

### **2. Configurar variáveis de ambiente**
Crie um arquivo `.env` baseado no `.env.example` e configure:
```bash
cp .env.example .env
```

### **3. Subir os containers Docker**
```bash
docker-compose up -d --build
```

### **4. Gerar chave da aplicação**
```bash
docker-compose exec app php artisan key:generate
```

### **5. Rodar migrações e seeders**
```bash
docker-compose exec app php artisan migrate --seed
```

---

## 🔑 Autenticação
A API usa **Laravel Sanctum** para autenticação. Após registrar/login, é necessário enviar o **Bearer Token** nas requisições protegidas.

### **Registro de Usuário**
**Rota:** `POST /api/register`
```json
{
    "name": "Teste Testando",
    "email": "test1@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### **Login de Usuário**
**Rota:** `POST /api/login`
```json
{
  "email": "test1@example.com",
  "password": "password"
}
```

**Resposta:**
```json
{
  "token": "seu_token_aqui",
  "user":...
  ...
}
```

---

## 💰 Funcionalidades da Carteira
### **Ver saldo da carteira**
**Rota:** `GET /api/user/wallet`

### **Alterar status da carteira para inativa**
**Rota:** `PUT /api/user/wallet/`
```json
{
  "status": 0
}
```

---

## 🔄 Transações
### **Depositar dinheiro**
**Rota:** `POST /api/user/transaction/deposit`
```json
{
  "amount": 100.00
}
```

### **Transferir dinheiro**
**Rota:** `POST /api/user/transaction/transfer`
```json
{
  "amount": 50.00,
  "wallet_id": 2
}
```

### **Histórico de Transações**
**Rota:** `GET /api/user/transaction/history`

### **Cancelar Transação**
**Rota:** `PUT /api/user/transaction/cancel/{id}`

---

## ✅ Testes Automatizados
Para rodar os testes, execute:
```bash
docker-compose exec app php artisan test
```
Ou execute testes específicos:
```bash
docker-compose exec app php artisan test --filter=TransactionControllerTest
```
---

## 📜 Licença
Este projeto é de código aberto sob a licença MIT.

---

## 📬 Contato
Se tiver dúvidas, entre em contato via [seuemail@example.com](nondasnoronha@example.com).

