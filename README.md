makelab
=======

makelab은 워드프레스 테마 [make](//github.com/thethemefoundry/make)의 하위 테마이다.

사용자 스타일시트와 스크립트 사용하기
-------------------------------------

1. 테마 디렉토리 안에 custom 디렉토리를 생성한다.
2. custom 디렉토리에 필요한 파일을 작성합니다. 인식할 수 있는 파일의 종류는 다음과 같습니다.
    * functions.php -- 테마 functions.php 내부에서 인클루드됩니다. ML_MAP, ML_MAP_PATCH 상수를 정의해서 의존성 스타일시트 구성을 변경할 수 있습니다.
    * style.css -- 태마 스타일시트에 이어서 로드됩니다.
    * script.js -- 테마 스크립트에 이어서 로드됩니다.
    * footer.html -- 테마 풋터에 이어서 출력됩니다. 이 파일이 존재하지 않으면 jframework footer(`jframework/html/global-footer/*.html)를 출력합니다.

