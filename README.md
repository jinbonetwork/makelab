Makelab
=======

`Makelab`은 워드프레스 테마 [Make](//github.com/thethemefoundry/make)의 하위 테마이다.

고치거나 추가한 기능
--------------------

1. `Make` 테마와 `Make Plus` 플러그인의 한글 언어팩
2. [jFramework](//github.com/jinbonetwork/jframework)와 [LESS](//lesscss.org)를 사용 
3. 블로그, 아카이브, 검색 결과, 게시물, 페이지의 등록일 옆에 수정일을 표시하는 *사용자 정의 옵션*
4. 등록일과 수정일을 사용자 정의 필드를 이용해서 강제로 덮어 쓸 수 있다. (`published_date`, `modified_date`)
5. 빌더 섹션에 사용자 클래스와 속성을 지정할 수 있는 *편집 옵션*
6. `jFramework`에 포함된 풋터를 켜거나 끄는 *사용자 정의 옵션*
7. 프론트엔드에서 *사용자 스타일시트*와 *사용자 스크립트*를 읽어들임 (후술)

사용자 스타일시트와 스크립트 사용하기
-------------------------------------

1. 테마 디렉토리 안에 `custom` 디렉토리를 생성한다.
2. `custom` 디렉토리에 필요한 파일을 작성한다. 인식할 수 있는 파일의 종류는 다음과 같다.
    * `functions.php` -- 테마 `functions.php`에 이어서 인클루드된다.
    * `style.css` -- 태마 스타일시트에 이어서 로드된다.
    * `script.js` -- 테마 스크립트에 이어서 로드된다.
    * `footer.html` -- 테마 풋터에 이어서 출력된다.

