<?php
/*
$question = "世界末日是什么时候";
$answers = array('2012', '2013', '2014');
$answer = new Answer($question, $answers);
$answer->printAppearTime();
$answer->printBestAnswer();
*/


class Answer{
	private $questionWord; //问题文本
	private $answerWords = array(); //存放每个答案的数组
	private $answerAppearTimes = array(); //存放每个答案及其对应出现次数的数组
	
	/**
	 * 构造方法
	 */
	public function __construct($questionWord, $answerWords){
		$this->setQuestionWord($questionWord, $answerWords); //设置问题、答案文本
		$this->countAnswerAppearTime(); //统计出现次数
	}
	
	/**
	 * 设置问题文本
	 */
	public function setQuestionWord($questionWord, $answerWords){
		$this->questionWord = $questionWord;
		if(!is_array($answerWords))
		{
			die("第二个参数非数组");
		}
		$this->answerWords = $answerWords;
	}
	
	/**
	 * 设置问题对应的百度搜索url
	 * @return 生成的url文本
	 */
	private function createQuestionURL(){
		$url = "https://www.baidu.com/s?ie=utf8&oe=utf8&wd=";
		$url .= $this->questionWord;
		$url .= "&tn=98012088_6_dg&ch=50";
		return $url;
	}
	
	/**
	 * 获取问题搜索页面的html代码
	 * @return 获取的html文本
	 */
	private function getAnswerHTML($url){
		$header = array (
        "Host:www.baidu.com",
        "Content-Type:application/x-www-form-urlencoded",//post请求
        "Connection: keep-alive",
        'Referer:http://www.baidu.com',
        'User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; BIDUBrowser 2.6)'
		);
    	$ch=curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置HTTP头部
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
    	$result=curl_exec($ch);
    	return $result;
	}
	
	/**
	 * 统计所提供的答案在页面上的出现次数
	 */
	private function countAnswerAppearTime(){
		$content = $this->getAnswerHTML($this->createQuestionURL());
		foreach($this->answerWords as $k => $v)
		{
			$this->answerAppearTimes[$v] = substr_count($content, $v);
		}
	}
	
	/**
	 * 输出每个答题出现的次数
	 */
	public function printAppearTime(){
		foreach($this->answerAppearTimes as $k => $v)
		{
			echo "选项 {$k} 出现次数 {$v} \n";
		}
	}
	
	/**
	 * 输出最佳答案
	 */
	public function printBestAnswer()
	{
		$bestAnswers = array_keys($this->answerAppearTimes, max($this->answerAppearTimes));
		echo "推荐答案：{$bestAnswers[0]}";
	}
	 
}





?>