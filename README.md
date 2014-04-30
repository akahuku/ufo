# UFO: unicode bitmap font



## これは何？

UFO は、BDF 形式のフォントです。以下の特徴があります:

* Unicode の Basic Multilingal Plane をサポート
* 端末エミュレータで使用することを想定した 8x16 と 16x16 の文字で構成される等
幅フォント



## それって Unifont がすでにあるよね。何が違うの？

GNU のプロダクトである Unifont もまた、BMP の全領域をサポートするフォントであ
り、その中には BDF フォントも含まれます。しかし、Unifont は CJK 系のコードポ
イントについては WenQuanYi Project 由来のグリフを利用しており、これは日本語の
文の表示にはあまり向いていません。

UFO はその点を解決するために製作されたフォントです。



## UFO フォントのグリフはすべてあなたが作ったの？

UFO は一部のコードポイントについて独自にデザインしたグリフを用いていますが、
それ以外のコードポイントについては既存のフリービットマップフォントを大いに利
用しています。

* Unifont <GPL v2.0 with font embedding exception>

  unifont-6.3.20140214.bdf
  http://unifoundry.com/unifont.html

* jiskan16 <Public Domain>

  jiskan16-2004-1.bdf
  jiskan16-2000-2.bdf
  http://www12.ocn.ne.jp/~imamura/jisx0213.html

* 東雲フォント <Public Domain>

  shnmk16.bdf
  http://openlab.ring.gr.jp/efont/shinonome/

* UFO 独自のグリフ

  どのコードポイントが UFO 独自のグリフかは、
  gryph/05-ufo/ 以下の png イメージを参照してください。


グリフの選択はこの順の通り、Unifont が最も低い優先度、下に行くに従ってグリフ
が gryph/ 以下の png イメージに定義されていればそれを優先、という形式になって
います。



## ライセンスは？

Unifont のグリフを使用していますので、UFO も

  GPL v2.0 with font embedding exception

になります。詳細は COPYING ファイルを参照してください。
