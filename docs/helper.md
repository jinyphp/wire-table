# 테이블 헬퍼
테이블 조작을 위한 헬퍼 함수를 제공합니다.

## 테이블 rows 갯수 count
테이블에서 rows의 갯수를 파악할 수 있는 헬퍼 함수 입니다.

### table_count()
테이블에서 rows의 갯수를 반환합니다.
```php
table_count('shop_categories')
```

### table_enable_count()
`enable`필드가 있는 테이블에서, enable 필드가 활성화된 rows의 갯수를 반환합니다.

```php
table_enable_count('shop_categories')
```
