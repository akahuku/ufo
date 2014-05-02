# UFO: Unicode Font, Organized



## これは何？

UFO は、BDF 形式のフォントです。以下の特徴があります:

* Unicode の Basic Multilingal Plane をサポート
* 端末エミュレータで使用することを想定した 8x16 と 16x16 の文字で構成される
等幅フォント



## それって Unifont がすでにあるよね。何が違うの？

GNU のプロダクトである Unifont もまた、BMP の全領域をサポートするフォントであ
り、その中には BDF フォントも含まれます。しかし、Unifont は CJK 系のコードポ
イントについては WenQuanYi Project 由来のグリフを利用しており、これは日本語の
文の表示にはあまり向いていません。

UFO はその点を解決するために製作されたフォントです。

### 英語のグリフ

[Latin gryph](http://appsweets.net/ufo/latin.png "Latin gryph")

### 日本語のグリフ

[日本語のグリフ](http://appsweets.net/ufo/japanese.png "日本語のグリフ")

### ソースコード

[ソースコード](http://appsweets.net/ufo/source-code.png "ソースコード")



## UFO フォントのグリフは誰が作ったの？

UFO は一部のコードポイントについて私（http://akahuku/github.com）が独自に
デザインしたグリフを用いていますが、それ以外のコードポイントについては既存の
フリービットマップフォントを大いに利用しています。
"UFO" のネーミングの由来もまたそこにあります。

* Unifont by GNU (GPL v2.0 with font embedding exception)

  unifont-6.3.20140214.bdf
  http://unifoundry.com/unifont.html

* Bitmap Song by Wen Quan Yi Project (GPL v2.0 with font embedding exception)

  wenquanyi_11pt.bdf
  http://wenq.org/wqy2/index.cgi?action=browse&id=Home&lang=en

* 東雲フォント (Public Domain)

  shnmk14.bdf
  http://openlab.ring.gr.jp/efont/shinonome/

* UFO 独自のグリフ

  どのコードポイントが UFO 独自のグリフかは、
  gryph/05-ufo/ 以下の png イメージを参照してください。


グリフの選択はこの順の通り、Unifont が最も低い優先度、下に行くに従ってグリフ
が gryph/ 以下の png イメージに定義されていればそれを優先、という形式になって
います。



## ライセンスは？

Unifont および Wen Quan Yi のグリフを使用していますので、UFO も

  GPL v2.0 with font embedding exception

になります。詳細は COPYING ファイルを参照してください。
