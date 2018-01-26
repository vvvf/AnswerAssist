# AnswerAssist

基于tesseract的答题辅助。

原理是使用adb截屏后，调用tesseract识别问题文字，然后百度统计词频。

调用tesseract使用的是[thiagoalessio](https://github.com/thiagoalessio)的tesseract-ocr-for-php，可以直接在用Composer导入。


tesseract识别中文准确率不高，所以这个程序没有太大实用意义。# AnswerAssist
