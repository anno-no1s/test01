/**************************************************

	2018年8月テスト 問1プログラム

**************************************************/

const {google} = require('googleapis');

const apiKey = '';

const sheets = google.sheets({
  version: 'v4',
  auth   : apiKey,
});

sheets.spreadsheets.values.get({
  spreadsheetId: '11BCnspCt2Mut3nhc4WMY6CYTd0zF9C3eCzsk1AEpKLM',
  range        : 'A1:E6',
}, (err, res) => {
  if (err) {
    return console.log('The API returned an error: ' + err);
  }
  const rows = res.data.values;
  if (rows.length) {
    rows.map((row) => {
      console.log(`\'${row[0]}\',\'${row[1]}\',\'${row[2]}\',\'${row[3]}\',\'${row[4]}\',`);
    });
  } else {
    console.log('No data found.');
  }
});
