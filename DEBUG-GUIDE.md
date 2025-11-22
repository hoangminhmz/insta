# 🔍 Debug Guide - Quick Troubleshooting

Nếu gặp lỗi "Failed to parse AI response as JSON", dùng các tools này để tìm nguyên nhân.

## 🚀 Quick Start (3 bước)

### Bước 1: Test API đơn giản
```
Truy cập: https://yourdomain.com/test-simple.php
```
**Tool này sẽ:**
- Test API với prompt đơn giản
- Show từng bước chi tiết
- Hiển thị exact response từ Gemini
- Cho biết JSON có parse được không
- Plain text output, dễ đọc

**Kết quả mong đợi:**
```
STEP 8: Parse Generated Text as JSON
---------------------
SUCCESS! JSON parsed:
Array
(
    [status] => ok
    [test] => success
)
```

### Bước 2: Xem error logs
```
Truy cập: https://yourdomain.com/view-logs.php
```
**Tool này sẽ:**
- Tìm và hiển thị PHP error logs
- Filter errors liên quan Gemini
- Show 100 dòng gần nhất
- Test button để generate carousel
- Links đến các debug tools khác

### Bước 3: Debug tool đầy đủ
```
Truy cập: https://yourdomain.com/debug-api.php
```
**Tool này sẽ:**
- Visual interface đẹp
- Test API với/không có responseMimeType
- Show raw response
- Parse structure
- Performance metrics

## 📊 Các lỗi thường gặp

### Lỗi 1: "API key not configured"
**Nguyên nhân:** Chưa set API key trong config.php

**Fix:**
```php
// config.php
define('GEMINI_API_KEY', 'your-actual-api-key-here');
```

### Lỗi 2: "HTTP 404: model not found"
**Nguyên nhân:** Model name không đúng hoặc không available

**Fix:** Thử models khác trong config.php:
```php
// Option 1: Gemini 2.5 Flash
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent');

// Option 2: Gemini 2.0 Flash Experimental
// define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent');
```

### Lỗi 3: "Failed to parse as JSON: Syntax error"
**Nguyên nhân:** Response có markdown wrapper hoặc extra text

**Kiểm tra bằng test-simple.php:**
- Xem STEP 7: Generated text
- Nếu có `​```json` wrapper → cần clean
- Nếu không có `{` ở đầu → response sai format

**Fix tự động:** Code đã có logic cleaning trong generate.php (line 290-303)

**Fix manual nếu vẫn lỗi:**
1. Xem exact response từ test-simple.php
2. Check xem `responseMimeType` có work không
3. Thử models khác nếu current model không support

### Lỗi 4: Response có text thêm ngoài JSON
**Ví dụ response:**
```
Here is the JSON:
{"slide1": {...}}
Hope this helps!
```

**Fix:** Code đã extract JSON bằng tìm `{` đầu và `}` cuối (line 298-302)

## 🎯 Workflow Debug

```
1. Run test-simple.php
   ↓
2. Nếu SUCCESS → Main app should work
   Nếu FAILED → Xem exact error
   ↓
3. Check error logs (view-logs.php)
   ↓
4. Try different model (config.php)
   ↓
5. Test lại với debug-api.php
```

## 📝 Debug Checklist

- [ ] API key đã set trong config.php
- [ ] test-simple.php shows SUCCESS
- [ ] Model name đúng trong config.php
- [ ] responseMimeType = 'application/json' (line 222)
- [ ] PHP version >= 8.0
- [ ] cURL extension enabled
- [ ] Internet connection working

## 🔧 Advanced Debug

### View raw API response in browser:
```php
// Temporary add to generate.php after line 247:
file_put_contents('debug-response.json', $response);
```

Sau đó access: `https://yourdomain.com/debug-response.json`

### Enable verbose error logging:
```php
// config.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
```

## 💡 Tips

1. **Test simple first:** Luôn test với test-simple.php trước
2. **Check logs:** view-logs.php sẽ show exact error
3. **Try models:** Nếu model không work, thử model khác
4. **Clean cache:** Clear browser cache nếu code đã update
5. **Reload page:** Sau khi update code, reload trang

## 🆘 Vẫn không work?

1. Copy output từ test-simple.php
2. Copy error từ view-logs.php
3. Check response có format gì
4. Report lại với exact error message

## ✅ Success Indicators

Khi mọi thứ hoạt động đúng:

**test-simple.php:**
```
STEP 8: Parse Generated Text as JSON
---------------------
SUCCESS! JSON parsed:
```

**Main app:**
- Generate carousel thành công
- Show 10 slides preview
- Có thể customize design
- Download ZIP works

Chúc may mắn! 🚀
