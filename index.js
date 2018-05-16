/**************************************************

	2018年5月テスト 問1プログラム

**************************************************/

const fs = require('fs');
const path = require('path');
const request = require('request');
const twitter = require('twitter');
const url = require('url');

// 各情報を設定してください。
const consumerKey = '';       // CONSUMER_KEY
const consumerSecret = '';    // CONSUMER_SECRET
const accessTokenKey = '';    // ACCESS_TOKE
const accessTokenSecret = ''; // ACCESS_TOKEN_SECRET

console.log('Start!');

const client = new twitter({
  consumer_key: consumerKey,
  consumer_secret: consumerSecret,
  access_token_key: accessTokenKey,
  access_token_secret: accessTokenSecret
});

searchTweet();

function searchTweet() {
  const params = {
    q: 'JustinBieber filter:images exclude:retweets',
    count: 20
  };
  client.get('search/tweets', params) 
    .then(function (tweets) {
      let imageUrls = [];
      tweets.statuses.forEach(function (tweet) {
        const media = tweet.entities.media;
        if (media && media[0].media_url && imageUrls.indexOf(media[0].media_url) === -1) {
          imageUrls.push(media[0].media_url);
        }
      });
      for(let i = 0; i < 10; i++){
        saveImage(imageUrls[i], i);
      }
    })
    .then(function (response) {
      console.log('End!');
    })
    .catch(function (error) {
      console.log(error);
    });
}

function saveImage(imageUrl, imageName) {
  const parsedUrl = url.parse(imageUrl);
  const ext = path.extname(parsedUrl.pathname);
  request(
    {method: 'GET', url: imageUrl, encoding: null},
    function (error, response, body) {
      if (!error && response.statusCode === 200) {
        fs.writeFileSync('img/'+imageName+ext, body, 'binary');
      }
    }
  );
}
