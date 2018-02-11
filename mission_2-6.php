<?php
header("Content-type:text/html;charset=utf-8");//文字コードを指定

$time = date ("Y年m月d日H時i分");//投稿日時

//１．入力フォームの作成
$rp = touch( 'kadai2-6.txt' );//テキストファイルを作成する
$filename = 'kadai2-6.txt';//テキストファイル名を変数に格納

if ( !empty ( $_POST['名前'] ) && !empty ( $_POST['コメント'] )) {//フォームにデータがセットされた時のみ処理を実行する
	//入力フォームから入力された文字列データをテキストファイルに保存(その際、1行ごとに「{番号}<>{名前}<>{コメント}<>{投稿された時間}」と言う形で保存する)
	if ( empty ( $filename )){
	//入力された文字列データをテキストファイルに追記する
	$fp = fopen ( $filename,"a" );
	fwrite ( fp,"1".'<>'.$_POST['名前'].'<>'.$_POST['コメント'].'<>'.$time.'<>'.$pass.'<>'."\n" );
	fclose ( $fp );
	}//if ( empty ( $filename )){
	else{
	$lines = file ( $filename );//ファイルを配列化する
	$number = count ( $lines );//配列の数を変数に代入する
	$fp = fopen ( $filename,"a" );//入力された文字列データをテキストファイルに追記する
	fwrite ( $fp,++$number.'<>'.$_POST['名前'].'<>'.$_POST['コメント'].'<>'.$time.'<>'.$pass.'<>'."\n" );
	fclose ( $fp );
	}//else{
}//if ( !empty ( $_POST['名前'] ) && !empty ( $_POST['コメント'] )) {


//２.削除フォームの作成
//POST送信にて削除番号を送信する。その際if文でaの削除フォームから値が送信された場合のみの処理に分岐させておく
if ( !empty ( $_POST['削除'] ) && !empty ( $_POST['パスワード'] )){//削除対象番号とパスワードが送信された場合のみの処理に分岐
	$filedata = file ( $filename ); //テキストファイルの内容を一行ずつの配列にしたものを別の変数に代入
	$fp = fopen ( $filename,"w" ); //テキストファイルの内容を消して開く
	foreach ( $filedata as $line ){ //配列から一つずつ取り出す
	$data = explode( '<>',$line ); //<>で切って配列に
			if( ( $data[0] != $_POST['削除'] ) && ( $data[4] != $_POST['パスワード'] )){ //削除対象番号とパスワードが一致しない時にカッコ内を処理
			fwrite ( $fp, $line ); //元のデータを再びファイルに書き込み
			}//if( ( $data[0] != $del ) && ( $data[4] = $pass )){
		fclose ($fp);
	}//foreach ( $filedata as $line ){
}//if ( !empty ( $_POST['削除'] ) && ( !empty ( $_POST['パスワード'] )){


//３.編集フォームの作成
//エラー防止のため、変数に代入する分岐前に変数を定義
//→分岐の中で変数を作っているので、「分岐を通らなかったとき」に、「表示時点までに変数が作られていない（＝定義されていない）」ため
$edit_num = "";//変数 = 編集する投稿番号
$user = "";//変数 = 名前
$text = "";//変数 = コメント

if ( !empty ( $_POST['編集対象番号'] ) && !empty ( $_POST['パスワード'] )){//編集番号とパスワードが送信された場合のみ処理を実行
//echo "編集に入りました";//デバッグ領域
$filedata = file ( $filename );//一行ずつの配列
foreach ( $filedata as $line ){//配列から一つずつ取り出す
$data = explode( '<>',$line );//<>で切って配列に
	if ( $_POST['パスワード'] == $data[4] ){//入力したパスワードと一致するときのみ以下を処理
		if( $_POST['編集対象番号'] == $data[0] ){//POSTで送信された編集対象番号と各投稿番号を比較し、イコールの時のみ処理を実行

		//デバッグ領域
		//echo "比較：";
		//var_dump($edit);
		//echo "<br>";
		//var_dump($data);
		//echo "<hr>";
		
		//変数に格納してフォームに表示
		$edit_num = $data[0];//編集する投稿番号を取得（hidden）
		$user = $data[1];//名前の内容を取得
		$text = $data[2];//コメントの内容を取得
		
		}//if( $edit == $num ){
	}//if ( $data[4] = $pass ){
}//foreach ( $filedata as $line ){
}//if ( !empty ( $_POST['編集対象番号'] ) && !empty ( $_POST['パスワード'] )){

if ( !empty ($_POST['編集name']) && !empty ($_POST['編集comment'])){//編集用フォームから編集後の内容が送信されたときのみ処理を実行
//変数の中身を編集前から編集後に置き換える
$user = $_POST['編集name'];
$text = $_POST['編集comment'];

//編集後のデータを組み合わせて変数に格納
$filedata = file( $filename );//テキストファイルを配列化して変数に格納
$fp = fopen( $filename, "w+");//元のファイルを(読み書き可能な状態にして)空にして開く
foreach( $filedata as $line ){//配列から一つずつ取り出す
$data = explode( '<>',$line );//<>で切って配列に

//編集する投稿番号、パスワードを取得（hidden）
$edit_num = $data[0];
$pass = $data[4];

	if( $data[0] == $edit_num ){//投稿番号がPOSTで送信された編集番号と同じなら処理
	//echo "投稿の書き換え";//デバッグ領域				
	fputs($fp,$edit_num.'<>'. $user.'<>'. $text.'<>'.$time.'<>'.$pass.'<>'."\n");//編集後のデータをファイルに追記
	}//if( $data[0] == $edit_num )
	else {//一致しない場合は以下のように処理
	fputs($fp,$line);//元の一行をファイルに追記
	}//else {
}//foreach( $filedata as $line ){
fclose($fp);//ファイルを閉じる
}//if ( !empty($_POST['編集name']) && !empty($_POST['編集comment'] )){

//エラーメッセージの表示
error_reporting(E_ALL);
ini_set('display_errors', 'On');

?>



<!DOCTYPE html><!-HTMLの形式->
<html lang="ja"><!-使用言語（日本語）->

<head>
<meta charset="UTF-8"><!-エンコード->
<meta name="description" content="mission_2-6">
<title>mission_2-6</title><!-ブラウザの上部に表示されるページのタイトル->
</head>

<body>
<h1>mission_2-6</h1>

	<!-入力フォーム->
	<form action="" method="post">
		<h3>≪コメントの投稿≫</h3>
		&nbsp;&nbsp;名&nbsp;&nbsp;前&nbsp;&nbsp;：&nbsp;<input type="text" name="名前" size=""><!-名前入力欄->
		<br>
		<br>
		&nbsp;コメント&nbsp;：&nbsp;<input type="text" name="コメント" size=""><!-コメント入力欄->
		<br>
		<br>
		&nbsp;Pass&nbsp;：&nbsp;<input type = "text" name = "パスワード"><!-Passの入力フォーム->
		<br>
		<br>
		&nbsp;<input type="submit"value="投稿する">&nbsp;&nbsp;<input type="reset"value="リセット"><!-送信・リセットボタン->
		<br>
		<br>
		<br>
	</form>


	<!-2-1の入力フォームとは別に、削除番号指定用フォームを用意する。入力項目は「削除対象番号」->
	<form action="" method="post">
		<h3>≪削除したい投稿を指定してください。≫</h3>
		投稿番号&nbsp;：&nbsp;<input type="text" name="削除" size=""><!-削除番号入力欄->
		&nbsp;Pass&nbsp;：&nbsp;<input type = "text" name = "パスワード"><!-Passの入力フォーム->
		<br>
		<br>
		&nbsp;<input type="submit"value="削除する"><!-実行ボタン->
		<br>
		<br>
	</form>

	<!-2-1および2-4の入力／削除フォームとは別に、編集番号指定用フォームを用意する。項目は「編集対象番号」->
	<form action="mission_2-6.php" method="post">
		<h3>≪編集したい投稿を指定してください。≫</h3>
		投稿番号&nbsp;：&nbsp;<input type = "text" name = "編集対象番号" size = ""><!-編集番号入力欄->
		&nbsp;Pass&nbsp;：&nbsp;<input type = "text" name = "パスワード"><!-Passの入力フォーム->
		<br>
		<br>
		&nbsp;<input type="submit" value="編集する"><!-実行ボタン->
		<br>
	</form>

	<form action="mission_2-6.php" method="post">
		<h4>[ 編集用フォーム ]</h4>
		<input type = "hidden" name = "編集対象番号" value = "<?=$edit_num;?>"<!-編集対象番号の送信->
		&nbsp;&nbsp;名&nbsp;&nbsp;前&nbsp;&nbsp;：&nbsp;<input type = "text" name = "編集name" value = "<?=$user;?>"><!-入力フォームに編集する名前を表示->
		<br>
		<br>
		&nbsp;コメント&nbsp;：&nbsp;<input type = "text" name = "編集comment" value = "<?=$text;?>"><!-入力フォームに編集するコメントを表示->
		<br>
		<br>
		&nbsp;<input type="submit"value="送信"><!-送信ボタン->
		<br>
		<br>
	</form>

</body>

</html>



<?php
//文字コードを指定
header("Content-type:text/html;charset=utf-8");

//４.テキストファイルを読み込み、フォームのすぐ下に表示する。
$lines = file ( $filename );//a.読み込みの際はfile関数を用いれば、簡単に配列として読み込める
foreach ( $lines as $key => $value ){//b.読み込んで取得した配列を、配列の数（行数分）だけループさせる（繰り返し処理する）
$pieces = explode( '<>',$value );//c.さらに記号「<>」で分割することでそれぞれの値を取得する(explodeを使う)
echo 'No.'.$pieces[0].'&nbsp;'.'投稿者：'.$pieces[1]."<br/>".'&nbsp;'.$pieces[2].'&nbsp;'.'&nbsp;'."<br/>".$pieces[3]."<br/><br/>\n";//d.取得した値をecho等を用いて表示する（※このとき区切り文字である「<>」は入れないこと）
}//foreach ( $lines as $key => $value ){

//エラーメッセージの表示
error_reporting(E_ALL);
ini_set('display_errors', 'On');

?>