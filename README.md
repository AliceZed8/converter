# converter

### Инструкция по запуску
```
git clone https://github.com/AliceZed8/converter.git
cd converter
sudo docker compose build && sudo docker compose up -d
```

### Описаниe АПИ
- Для получения котировок
```
curl -X GET localhost:8080/api/get_quotes

-> ["EUR", "USD", ...]
```
- Для добавления котировки
```
curl -X POST localhost:8080/api/add_quote -d '{"currency":"USD", "rate":1.09}'

-> {"status": "..."}
```
- Для удаления котировки
```
curl -X POST localhost:8080/api/remove_quote -d '{"currency":"USD"}'

-> {"status": "..."}
```
- Для обновления котировки
```
curl -X POST localhost:8080/api/update_quote -d '{"currency":"USD", "rate":1.09}'

-> {"status": "..."}
```
- Для получения курса обмена
```
curl -X POST localhost:8080/api/get_exchange_rate -d '{"from":"USD", "to": "EUR"}'

-> {"status": "...", "exchange_rate": 0.01}
```