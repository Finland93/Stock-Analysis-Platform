var apiKey = "<?php include '../app/db-config.php'; echo $av_api_key; ?>";
var newsModal = document.querySelector("#news-modal");
var closeBtn = document.querySelector(".close-btn");
var newsList = document.querySelector("#news-list");

var btn = document.querySelectorAll(".news-button");
for (const button of btn) {
  button.addEventListener("click", function() {
    symbol = this.getAttribute("news-symbol");
	newsModal.querySelector("h2").textContent = "Latest News for " + this.getAttribute("news-name");
    fetchNews(symbol);
    newsModal.style.display = "block";
  });
}


closeBtn.addEventListener("click", function() {
  newsModal.style.display = "none";
});

window.addEventListener("click", function(event) {
  if (event.target == newsModal) {
    newsModal.style.display = "none";
  }
});


async function fetchNews(symbol) {
  const response = await fetch(`https://www.alphavantage.co/query?function=NEWS_SENTIMENT&tickers=${symbol}&apikey=${apiKey}`);
  const data = await response.json();
  if (!data.hasOwnProperty("feed")) {
    console.error("Data does not have a 'feed' property");
    return;
  }
  const news = data.feed.slice(0, 6);

  newsList.innerHTML = "";
  


const sentimentScoreDefinition = document.createElement("p");
sentimentScoreDefinition.innerHTML = `<b>Sentiment:</b> Bearish (x <= -0.35) | <b>Somewhat-Bearish</b> (-0.35 < x <= -0.15) | Neutral (-0.15 < x < 0.15) | <b>Somewhat-Bullish</b> (0.15 <= x < 0.35) | Bullish (x >= 0.35)`;
newsList.appendChild(sentimentScoreDefinition);

const sentimentDefinition = document.createElement("p");
sentimentDefinition.innerHTML = `<b>Bullish meaning:</b> Expecting prices to rise over a certain period of time | 
<b>Bearish meaning:</b> Expecting prices to decrease over a certain period of time. `;
newsList.appendChild(sentimentDefinition);

	var counter = 0;
for (const newsItem of news) {
const listItem = document.createElement("div");
listItem.classList.add("col-md-4");

  let timePublished = "N/A";
  if (newsItem.hasOwnProperty("time_published")) {
    const date = new Date(newsItem.time_published.substring(0, 4) + "-" +
                         newsItem.time_published.substring(4, 6) + "-" +
                         newsItem.time_published.substring(6, 8));
    timePublished = `${date.getDate()}.${date.getMonth() + 1}.${date.getFullYear()}`;
  }

  let overallSentimentScore = "N/A";
  if (newsItem.hasOwnProperty("overall_sentiment_score")) {
    overallSentimentScore = newsItem.overall_sentiment_score.toFixed(2);
  }

  let overallSentimentLabel = "N/A";
  if (newsItem.hasOwnProperty("overall_sentiment_label")) {
    overallSentimentLabel = newsItem.overall_sentiment_label;
  }

  listItem.innerHTML = `
  <div class="spacer">
    <p class="newsHeading">${newsItem.title}</p>
    <p>${newsItem.summary}</p>
    <p>Time Published: ${timePublished}</p>
    <p>Overall Sentiment Score: ${overallSentimentScore}</p>
    <p>Overall Sentiment Label: ${overallSentimentLabel}</p>
  <a href="${newsItem.url}" target="_blank">Read article</a>
  </div>
  `;
  
if (counter === 0) {
const row = document.createElement("div");
row.classList.add("row");
newsList.appendChild(row);
row.appendChild(listItem);
counter++;
} else if (counter % 3 === 0) {
const row = document.createElement("div");
row.classList.add("row");
newsList.appendChild(row);
row.appendChild(listItem);
counter = 1;
} else {
const row = newsList.querySelector(".row:last-child");
row.appendChild(listItem);
counter++;
}

}

}