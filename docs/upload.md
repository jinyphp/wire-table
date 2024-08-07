# 이미지 파일 업로드
`wire-table`을 이용하여 이미지등과 같은 파일을 업로드 할 수 있습니다.

## 입력폼 설정

```php
<div class="mb-3">
    <label for="simpleinput" class="form-label">사진</label>
    <input type="file" class="form-control"
                wire:model.defer="forms.image">
    <p>
        @if(isset($forms['image']))
        {{$forms['image']}}
        @endif
    </p>
</div>
```


## 업로드 경로
별도의 설정이 없는 경우 파일은 `/storage/app/upload` 폴더에 업로드 됩니다. 만일 다른 경로에 업로드를 원하는 경우에는 별도의 지정을 할 수 있습니다.

### 라이브와이어
지니와이어를 상속받아 컴포넌트를 재구성할 경우, 라이브와이어 내에서 업로드하는 서브폴더 경로를 추가하는 방법입니다.

`upload_path` 프로퍼티값을 주게 되면, `/storage/app/upload/경로위치`로 업로드 됩니다. 
```php
$this->upload_path = "/경로위치";
```

### Actions값으로 서브폴더 지정
컨트롤러에서 `actions` 값을 이용하여 업로드 서브 폴터를 추가할 수 있습니다.

다음과 같이 `actions['upload']['path']` 속성을 지정합니다.
```php
$this->actions['upload']['path'] = "/경로위치";
```
actions 값을 추가하게 되면 `/storage/app/upload/경로위치`로 업로드 됩니다. 

### 업로드 이미지 response
업로드된 이미지들은 기본적으로 라라벨 저장소인 `/storage/app/upload` 안에 저장됩니다. 이는 외부의 public 접근이 제한된 서버 내부 저장소 입니다. 따라서, 이곳에 이미지를 저장하는 경우 외부 web에서 접근할 수 없는 단점이 있습니다.

이를 해결하기 위해서 `/upload`로 시작하는 라우트를 지원합니다. 이 uri는 `AssetsController` 컨트롤러와 연결이 되어 있으며, `/upload/~~~` 이후의 uri를 분석하여 저장된 이미지를 읽고, 이를 Http Response Body로 반환합니다.


## 이미지 복사
기본적으로 모든 업로드 파일은 `/storage/app` 안에 저장됩니다. 하지만, 임시 업로드 파일이 생성된 후에 다른 지정 폴더로 옴겨서 사용을 할 수도 있습니다.

`upload_move` 값이 설정되어 있는 경우, 임시파일을 해당위치로 이동을 처리합니다.
```php
$this->upload_move = "/경로위치";
```

또는

```php
$this->actions['upload']['move'] = "/경로위치";
```
> 경로의 기준위치는 `resources/www`하위에 복사됩니다.

