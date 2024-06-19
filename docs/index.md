# controllers
지니 WireTable은 `라이브와이어`와 다양한 작업을 위한 컨트롤러들을 제공합니다. 기존 라라벨의 기본 controller를 확장하여 각각의 동작에 맞도록 커스텀화된 기능으로 확장하여 제공합니다.

## 컨트롤러 설정
지니 WireTable은 컨트롤러의 동작 및 라이브와이어와의 소통을 위해서 공통의 `$actions` 변수를 가지고 있습니다. 이 변수에는 다양한 정보들이 배열 값으로 저장되 되어 있습니다.

`$actions` 는  컨트롤러의 생성자에서 필요한 정보들을 기록하고, 전달할 수 있습니다. 또는 `/resources/actions` 안에 uri와 매칭된 형태로 컨트롤러에 데이터 설정값만을 분리하여 전달할 수 있습니다.

## 컨트롤러
지니 WireTable은 다양한 종류의 컨트롤러를 제공합니다.

### WireDashController
데시보트를 처리하기 위한 컨트롤러 입니다. 데시보드 화면은 `$actions['view']['main']`에 지정을 해주어야 합니다.

### WireShowController
특정 테이블의 하나의 데이터 row를 읽어서 출력해 주는 컨트롤러 입니다.
이를 위해서는 먼저 테이블이 선택되어야 하며, 선택하고자 하는 `id`가 `uri`로 부여되어야 합니다.


### CRUD 컨트롤러
지니Table은 `livewire`를 활용하여 자동적으로 `CRUD`를 처리하는 컨트롤러를 제공합니다.  

테이블의 목록을 출력하고, 팝업창을 통하여 데이터를 입력 및 수정을 할 수 있습니다. CRUD 컨트롤러는 화면을 출력하는 형식에 따라서 Table 스타일과 Grid 스타일 2개를 제공합니다.

* WireTableController
* WireGridController

#### WireTableController
테이블 레이아웃으로 데이터 목록을 출력합니다.  
이 동작에 관련된 모든 리소스는 패키지의 `resources/view/table_popup_forms`안에 존재합니다.

#### WireGridController
그리드 헤이아웃으로 데이터 목록을 출력합니다.
이 동작에 관련된 모든 리소스는 패키지의 `resources/view/grids` 안에 존재합니다.


### 레이아웃 스위칭하기
각각의 컨트롤러는 레이아웃을 빠르게 스위칭 할 수 있는 `setLayout()` 메소드를 지원합니다.



## AssetsController
라이브와이어 테이블을 통하여 업로드한 이미지가 있는 경우, 이를 자동으로 출력할 수 있는 image response 입니다.

