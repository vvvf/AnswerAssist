<?php
require_once('./vendor/autoload.php');

use thiagoalessio\TesseractOCR\TesseractOCR;

/*
$question = new Question();
$question->display();
*/

class Question{
	private $questionWord;
	private $answerWords;
	private $picPath = __dir__ . '/Pic/screenshot.png';
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		if(!$this->screenshot())
		{
			die("请连接手机");
		}
		$this->cutPic();
		if($this->ocr() === false)
		{
			die('未识别到问题');
		}
	}
	
	/**
	 * 截取手机屏幕。这里仅安卓，需要adb
	 */
	private function screenshot(){
		$filePhone = "/sdcard/screenshot.png"; //截图在手机上的路径
		system("adb shell /system/bin/screencap -p {$filePhone}", $return_var);
		if(strpos($return_var, "device not found"))
		{
			return false; //未连接手机
		}
		system("adb pull {$filePhone} {$this->picPath}");
		return true;
	}
	
	/**
	 * 剪切图片，剪切截屏的问题区域，减少ocr识别时间，
	 * 不同手机不同答题软件可能需要调整数据
	 */
	private function cutPic(){
		
		$img = imagecreatefrompng($this->picPath);
		$img_x = 80; //开始剪切的坐标
		$img_y = 370;
		
		$new_img_width = 922; //生成的图片的宽高
		$new_img_height = 752;
		$new_img = imagecreate($new_img_width, $new_img_height);  
		imagecopyresampled($new_img, $img, 
							0, 0, 
							$img_x, $img_y, 
							$new_img_width, $new_img_height, 
							$new_img_width, $new_img_height);
		imagepng($new_img, $this->picPath);
		
		imagedestroy($new_img);
		imagedestroy($img);
		
	}
	
	/**
	 * 使用OCR识别图片上的问题
	 */
	private function ocr(){
		
		$res = (new TesseractOCR($this->picPath))->lang('chi_sim')->run();
		$res_r = explode("\r\n", $res);
		
	//	$this->questionWord = substr($temp, strpos($temp, '.') + 1, strpos($temp, '?') - 2);
		
		$res_r = array_filter($res_r);//剔除空元素
		
	//	var_dump($res_r);
		
	//	$flag = 0;
		foreach($res_r as $k => $v)
		{
		//	if($flag == 0 && strpos($v, '.'))//问题的第一行，这样写不好
		//	{
				$v = substr($v, strpos($v, '.') + 1);
		//		$flag = 1;
		//	}
		//	else if($flag == 0)
		//	{
		//		return false; //未识别到问题
		//	}
			$this->questionWord .= $v;
			unset($res_r[$k]);
			if(strpos($v, '?'))//问题的最后一行
			{
				break;
			}
		}
		
		$this->answerWords = array_values($res_r);
	//	var_dump($this->answerWords);
		return true;
	}
	/**
	 * 获取问题文本
	 */
	public function getquestionWord(){
		return $this->questionWord;
	}
	
	/**
	 * 获取答案选项
	 */
	public function getanswerWords(){
		return $this->answerWords;
	}
	
	/**
	 * 打印识别到的结果
	 */
	public function display(){
		echo "--------------------\n";
		echo "问题：{$this->questionWord}\n";
		echo "选项：\n" . implode($this->answerWords, "\n") . "\n------------------\n";
	}
}
?>