# WireTable-PopupForm


## 커스텀 디자인

### 레이아웃 변경
커스텀으로 외각의 레이아웃 스타일을 변경할 수 있습니다.
기본값으로는 `jiny-wire-table::table_popup_forms.admin` 과 `jiny-wire-table::table_popup_forms.layout` 입니다.

```php
## 레이아웃을 커스텀 변경합니다.
$this->actions['view']['layout'] = "jiny-shop-goods::admin.prices.layout";
```

레이아웃 작성 예제는 다음과 같습니다. 레이아웃에서 `WireTable-PopupForm` 라이브와이어 컴포넌트를 호출하여 삽입하는 것을 볼 수 있습니다.
```php
<x-theme theme="admin.sidebar">
    <x-theme-layout>

        {{-- Title --}}
        @if(isset($actions['view']['title']))
            @includeIf($actions['view']['title'])
        @else
            @includeIf("jiny-wire-table::table_popup_forms.title")
        @endif

        {{-- CRUD 테이블 --}}
        <section>
        <main>
            @livewire('WireTable-PopupForm', [
                'actions'=>$actions
            ])
        </main>
        </section>

    </x-theme-layout>
</x-theme>
```

