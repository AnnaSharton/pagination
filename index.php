<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Статьи PHP</title>
		<link rel="stylesheet" href="pag.css">
		<style>
			ul {list-style-type: none;padding: 0;}	
			h2 {text-align: center;}
			a {text-decoration: none;}
			a:hover {color: firebrick;}
			.open-article {display: flex; gap: 100px;}
			.sidebar {min-width: 30%; margin-top: 100px;}
			.sidebar p {font-weight: 700;}
			.container {display: flex; flex-direction: row; justify-content: start; gap: 100px; padding: 0 50px; width: 100%;}
			.add-article {padding: 50px 50px 0 0;}
			.pages__ul li {display:  inline-block; font-weight: 700; margin-right: 15px;}
			.pages__ul li:hover {margin-right: 15px; background: #dbe5eb; display: inline-block;}
			.pages__ul li a {display: block; padding: 10px;}
			input[type=submit] {cursor:pointer; border-radius: 3px; margin-top: 20px; padding: 10px;}
			input[type=text] {padding: 4px; width:100%;}
			input[type=date] {padding: 4px;}
			textarea {width:100%; resize: none;}
			.file-name {display: flex; justify-content: space-between; gap: 50px;}
			.link {display:inline;}
			.link-delete, .file-name span{font-size: 14px; color: gray;}
			.delete {font-size: 25px; border: 1px solid #000; background-color: lightgreen; padding: 20px;}
			.file-added {font-size: 20px;border: 1px solid #000;background-color: lightgreen;padding: 10px;}
			img {height:20px; margin-right: 10px; margin-bottom:-3px;}
			.form-containers {display: flex; gap:100px; justify-content: space-around; padding: 5px 30px;}
			.left-form, .right-form {width:80%; height: 80vh; padding: 25px;}
			.error {border: 1px solid gray; background: #f59393; padding: 5px;}
		</style>
	</head>
	<body>
		<?php 
			error_reporting(E_ALL);
			mb_internal_encoding("UTF-8");
			define('ROOT_DIR', dirname(__FILE__));  //с помощью dirname выделяю путь от корня до нужного файла и присваиваю для root_dir
			$dir='texts/';	
			
			//////  если задан гет-запрос show тогда вывожу содержимое статьи ///////
			if(isset($_GET['show'])){    
		?>
			<div class="open-article">
				<div class="left-article">
					<h1>Содержимое статьи:</h1>
					<?php 
						$text=file_get_contents(ROOT_DIR.'/'.$_GET['show']); // file_get_contents считывает текстовый файл и выводит все одной строкой
						$lines=explode("\n", $text); //разбиваю текст файла на массив чтобы получить первую строку и выводить ее в крошках
						$head=$lines[0]; //присваиваю названию статьи нулевую строку
						unset($lines[0]); //удаляю нулевую строку
						$text=implode('<br>', $lines); //мобираю текст в строку без нулевой строки
						$text='<p>'.str_replace("\n",'</p><p>', $text).'</p>'; //разбиваю строку на абзацы для красивого вывода статьи
					?>	
					
                <!--Хлебные крошки----->
                <a href="./index.php">Главная</a> > <?=$head?>
				<!--вывожу сначала первую строку-заголовок статьи:-->
					<br><br><div style="font-weight:700; font-size:22px;"><?=$head?></div>
				<!--затем вывожу время создания статьи статьи:-->
					<br><div style="color:green; font-weight:700;"><?=date ("Y-m-d", filectime(ROOT_DIR.'/'.$_GET['show']))?></div>
				<!--и вывожу содержимое статьи без заголовка:-->
					<?=$text?> 
				</div>
				<!--блок с информацией о времни изменения файлов filemtime и filectime-->
				<div class="sidebar">
					<p>Дата изменения статьи: <?php echo date ("Y-m-d", filemtime(ROOT_DIR.'/'.$_GET['show']));?></p>
					<p>Дата создания статьи: <?php echo date ("Y-m-d", filectime(ROOT_DIR.'/'.$_GET['show']));?></p>
					<a href="index.php">Вернуться на главную</a>
				</div> 
			</div>
			
				<?php } 
			//////////////////////////////передача гет-параметра file то удаляю статью  ////////////////////////////////
					else if (isset($_GET['file'])) {
						$fileDel = $_GET['file']; 
						unlink ($fileDel); // и если файл существует удаляю его
						echo '<div class="delete">Файл успешно удален</div>'; 
						echo '<br><p><a href="index.php">Вернуться на главную</a></p>';
					} 

			/////////    гет-параметр add вывожу формы добавления статьи   /////////////
					else if (isset($_GET['add'])) {
						//перед формой вывожу сообщения:
						if (isset($_GET['add']) and $_GET['add']==='uploaded') { //если все ок загружено и был редирект
							echo '<br><div class="file-added">Статья добавлена <a href="index.php">Вернуться на главную</a></div>';}
						else if (isset($_GET['add']) and $_GET['add']==='errors'){ //если нажали submit но забыли выбрать файл
							echo '<br><div class="error">Файл не выбран</div>';
							}
						?>
							<div class="form-containers">
								<!--  Форма простой загрузки  -->
								<div class="left-form">
									<h2>Загрузите статью</h2>
									<form method="post" action="" enctype="multipart/form-data"> 
										<h3>Выбрать файл:</h3>
										<input type="file" name="file"><br>
										<input type="submit" name="submit" value="Добавить"><br>
									</form>
									
										<?php /// обычная загрузка файла 
										if($_FILES and is_uploaded_file($_FILES['file']['tmp_name']) and isset($_POST['submit'])) { //превоеряю загружен ли файл
											
											move_uploaded_file($_FILES['file']['tmp_name'], $dir.$_FILES['file']['name']);
											header('Location: index.php?add=uploaded'); //редирект
										} else if ($_FILES and !is_uploaded_file($_FILES['file']['tmp_name']) and isset($_POST['submit']))  {
											header('Location: index.php?add=errors'); 
										}	
									?>
								</div>
								<div class="right-form">
									<!--  Форма создания статьи  -->
									<h2>Или добавьте статью вручную</h2>
									<form method="post" action="" name="create" enctype="multipart/form-data"> 
										<p>Название статьи:</p> <!--присваиваю имя h1 для поля ввода, h1 станет заголовком статьи-->
										<input type=text name="h1" placeholder="Введите название статьи" required><br>
										<p>Дата создания статьи:</p>
										<input type="date" name="article-date" value="<?php echo date("Y-m-d");?>" required>
										<p>Текст статьи:</p>
										<textarea name="article-text" placeholder="Введите текст тут" rows="10" required></textarea>
										<input type="submit" name="submit" value="Добавить"><br>
									</form>
								</div>
								
							</div><p style="margin-left:50px;"><a href="index.php">Вернуться на главную</a></p>
							
						<?php
						//добавление статьи вручную:
										if(isset($_POST['h1']) and isset($_POST['article-text']) and isset($_POST['submit'])) {
											$h1=$_POST['h1'];
											$textContent=$_POST['h1']."\n".$_POST['article-text']; //вношу с каждой строки заголовок,дату,текст				
											$newFile = fopen($dir.$h1.".txt","wb"); //mode=w, если файла не существует, fopen его создает
											fwrite($newFile,$textContent); //записываю контент в новый файл
											fclose($newFile);
											header('Location: index.php?add=uploaded'); //редирект
										} 
			}
			/////// а иначе вывожу каталог статей  ///////
				
			else {	?>
			<div class="container">
				<div class="right-side">
					<div class="catalog">
						<h1>Список статей</h1>
						<ul>

							<?php			
							$arrayFromFolder=scandir($dir);//создаю массив из папки articles

							 foreach (scandir($dir) as $file) { 
							  	if($file !="." && $file !="..") {
									 $files[filemtime($dir.$file)] = $file;
							  	} else {}
							  }
							  krsort($files); 
							  
							  foreach ($files as $file){
								$newA[]=$file;
							  }
			 				  $perPage=10; 
			 				  $pagesCount=ceil((count($newA))/$perPage); //округляю до большего кол-во элементов в массиве / кол-во элементов на страницу
			  				  $page=isset($_GET['page']) ? $_GET['page'] : 1; //проверяю существует ли параметр page и если да - присваиваю его переменной $page, если не существует - то номер страницы будет 1
			  				  $start=($page-1)*$perPage + 1; //на каждой странице каталог будет начинаться со стаьи № $start
			  				  $finish=$start+$perPage-1; //на статье № finish включительно закончится вывод каталога на 2ой странице
								
								$arrayFromFolder=scandir($dir);
								if ($handle = opendir($dir)){ //еще раз прохожу по массиву в папке articles
									$i=$start-1; 
									while(false !== ($arrayFromFolder=readdir($handle))){ //пока существует файл в папке
										if(scandir($dir) !="." && scandir($dir) !=".."){ //и если файл не равен . или ..
									   
											if ($i>=$start-1 && $i<=$finish-1 && $i<count($newA))  { //-1 т.к. индексы файлов на 1 меньше
												echo '
												<div class="file-name">
													<div><img src="https://cdn3.iconfinder.com/data/icons/arrow-outline-8/32/right_2-256.png"><a class="link" href="index.php?show='.$dir.$newA[$i].'">'.strstr((file_get_contents($dir.$newA[$i])), "\n" , true).'</a>
													- <span>'.date ("Y-m-d", filectime($dir.$newA[$i])).'</span></div>
													<div><a class="link link-delete" href="index.php?file='.$dir.$newA[$i]. '">Удалить</a></div>
												</div><br>'; 
										  } $i++;
										} 	
									}
								}
			  				  ?>
  
			 			</ul>
			  		</div>
			  		<div class="counter"> <!--создаю блок со счетчиком страниц-->
			  			<br>
			  			<div>Страницы:</div>
							<div class="pages">
								<ul class="pages__ul">
	
									<?php 
									for ($i=0; $i<$pagesCount; $i++) { //циклом вывожу список нужной длины
									?> 
										<li><a href="?page=<?=$i+1?>"><?= $i+1?></a></li> <!--вывожу счетчик в виде списка с с гет-запросом ?page=$i и плюсую 1 чтобы первая страница не начиналась с нуля-->
									<?php 
									}                   
									?>
	
								</ul>
								<br>
								<div>Всего статей: <?=count(scandir($dir))-2?></div>
							</div>
			  		  </div>
			  	    </div>
								  
			  	<div class="add-article">
					<div><a href="index.php?add="><input type="submit" id="submit" value="Добавить статью"></a></div>	  	
			  	</div>
			</div> 
		 					
		<?php}?>		
	</body>
</html>
