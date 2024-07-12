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
별도의 설정이 없는 경우 파일은 `/storage/app/upload` 폴더에 업로드 됩니다. 
업로드된 파일은 내부의 `AssetsController` 컨트롤러를 통하여 이미지를 response 할 수 있습니다.

만일 다른 경로에 업로드를 원하는 경우에는 별도의 지정을 할 수 있습니다.

### 고정폴더
고정된 경우에 업로드 할 수 있습니다. 이 방법은 라이브와이어 내에서만 가능한 프로퍼티 입니다.
```php
$this->upload_path = "경로위치";
```

### 서브폴더 추가
`actions` 값을 추가하여 서브 폴더를 추가할 수 있습니다.

```php
$this->actions['upload']['path'] = "경로위치";
```

위와 같이 actions 값을 추가하게 되면 `/storage/app/upload/경로위치`로 업로드 됩니다. 
