# 커스텀 Hook

라이브와이어에서 지정되지 않은 사용자 메소드를 호출할 수 있는 커스텀 hook을 지원합니다.

다음 예는 `WireTablePopupForm` 라이브와이어를 통하여 테이블을 호출할때, 호출된 컨트롤러의 특정 메소드를 역으로 호출할 수 있습니다.

```php
<td width='100'>
    <x-click wire:click="hook('rollback',{{$item->id}})">
        Rollback
    </x-click>
</td>
```

컨트롤러는 다음과 같이 선언합니다.
```php
public function rollback($wire, $args)
{
    dd($args);
}
```

