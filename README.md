# M3U PHP Parser M3U PHP 解析器

Parsing m3u with php.

用php解析m3u。



> Modify from https://github.com/onigetoc/m3u8-PHP-Parser
>
> 修改自https://github.com/onigetoc/m3u8-PHP-Parser



## New Features 新功能

- Support extracting EPG links in "x-tvg-url".
- Support extracting "http-referrer" and "http-user-agent" in "#EXTVLCOPT".



- 支持提取"x-tvg-url"里的EPG链接。
- 支持提取#EXTVLCOPT:里的"http-referrer"以及"http-user-agent"。



## Details 细节

convert http-referrer and http-user-agent in #EXTVLCOPT to #EXTINF attributes.

把#EXTVLCOPT里的http-referrer and http-user-agent转换到#EXTINF属性。

```php
$reg = '/(?:#EXTINF:(.+?)[,]\s?(.+?))(?:[\r\n]#EXTVLCOPT:(?:(http-referrer?|http-user-agent)=(.*[^\r\n])))/';

//convert http-referrer and http-user-agent in #EXTVLCOPT to #EXTINF attributes
//repeat twice to process http-referrer and http-user-agent
$m3ufile = preg_replace($reg, '#EXTINF:\\1 \\3="\\4",\\2', $m3ufile);
$m3ufile = preg_replace($reg, '#EXTINF:\\1 \\3="\\4",\\2', $m3ufile);

//remove empty lines
$m3ufile = preg_replace("/[\r\n]{2,}/", "\r", $m3ufile);
```





Use regular expressions to identify links in x-tvg-url and group them.

用正则表达式识别x-tvg-url里的链接，并分组。

```php
$tvgReg = '/#EXTM3U x-tvg-url="([^"]+)"/';
preg_match($tvgReg, $m3ufile, $tvgMatches);

preg_match_all('/https?:\/\/[^\s,]+/', $tvgMatches[1], $urls_matches);
//print_r($urls_matches[0]);

$epgUrls=$urls_matches[0];
//print_r($epgUrls);
```





## Result for parser.php 运行效果

M3U file example:

M3U 示例文件：

```
#EXTM3U x-tvg-url="http://epg.51zmt.top:8000/e.xml.gz,https://epg.112114.xyz/pp.xml.gz"
#EXTINF:-1 tvg-id="PhoenixChineseChannel.hk" tvg-name="凤凰中文" tvg-logo="https://i.imgur.com/rwY7FHT.png" group-title="通用",凤凰卫视中文
#EXTVLCOPT:http-referrer=http://www.stream-link.org/
#EXTVLCOPT:http-user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36
http://2022.stream-link.org/live/322460831
#EXTINF:-1 tvg-id="PhoenixInfoNewsChannel.hk" tvg-name="凤凰资讯" tvg-logo="https://i.imgur.com/mt4h3VO.png" group-title="新闻",凤凰卫视资讯
#EXTVLCOPT:http-referrer=http://www.stream-link.org/
#EXTVLCOPT:http-user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36
http://2022.stream-link.org/live/1770675188
#EXTINF:-1 tvg-id="TVBNewsChannel.hk" tvg-name="无线新闻" tvg-logo="https://i.imgur.com/Gwij0Fj.png" group-title="新闻",TVB 无线新闻台
#EXTVLCOPT:http-referrer=https://www.hklive.tv/
#EXTVLCOPT:http-user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36
https://cdn.hklive.tv/xxxx/83/index.m3u8
#EXTINF:-1 tvg-id="Jade.hk" tvg-name="翡翠台" tvg-logo="https://i.imgur.com/ooG96w8.png" group-title="通用",TVB 翡翠台
#EXTVLCOPT:http-referrer=http://www.stream-link.org/
#EXTVLCOPT:http-user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36
http://2022.stream-link.org/live/131217689
```





Output:

输出：

```json
{
    "list": {
        "service": "IPTV",
        "title": "IPTV",
        "EPG": [
            "http://epg.51zmt.top:8000/e.xml.gz",
            "https://epg.112114.xyz/pp.xml.gz"
        ],
        "item": [
            {
                "service": "IPTV",
                "title": "凤凰卫视中文",
                "playlistURL": "playlist.m3u",
                "media_url": "http://2022.stream-link.org/live/322460831",
                "url": "http://2022.stream-link.org/live/322460831",
                "id": "PhoenixChineseChannel.hk",
                "author": "凤凰中文",
                "thumb_square": "https://i.imgur.com/rwY7FHT.png",
                "group": "通用",
                "referrer": "http://www.stream-link.org/",
                "user-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36"
            },
            {
                "service": "IPTV",
                "title": "凤凰卫视资讯",
                "playlistURL": "playlist.m3u",
                "media_url": "http://2022.stream-link.org/live/1770675188",
                "url": "http://2022.stream-link.org/live/1770675188",
                "id": "PhoenixInfoNewsChannel.hk",
                "author": "凤凰资讯",
                "thumb_square": "https://i.imgur.com/mt4h3VO.png",
                "group": "新闻",
                "referrer": "http://www.stream-link.org/",
                "user-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36"
            },
            {
                "service": "IPTV",
                "title": "TVB 无线新闻台",
                "playlistURL": "playlist.m3u",
                "media_url": "https://cdn.hklive.tv/xxxx/83/index.m3u8",
                "url": "https://cdn.hklive.tv/xxxx/83/index.m3u8",
                "id": "TVBNewsChannel.hk",
                "author": "无线新闻",
                "thumb_square": "https://i.imgur.com/Gwij0Fj.png",
                "group": "新闻",
                "referrer": "https://www.hklive.tv/",
                "user-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36"
            },
            {
                "service": "IPTV",
                "title": "TVB 翡翠台",
                "playlistURL": "playlist.m3u",
                "media_url": "http://2022.stream-link.org/live/131217689",
                "url": "http://2022.stream-link.org/live/131217689",
                "id": "Jade.hk",
                "author": "翡翠台",
                "thumb_square": "https://i.imgur.com/ooG96w8.png",
                "group": "通用",
                "referrer": "http://www.stream-link.org/",
                "user-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36"
            }
        ]
    }
}
```



