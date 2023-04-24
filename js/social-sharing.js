function shareToTwitter() {
  const currentUrl = encodeURIComponent(window.location.href);
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    window.open(`twitter://post?message=${currentUrl}`, "_blank");
  } else {
    window.open(
      `https://twitter.com/intent/tweet?url=${currentUrl}`,
      "Twitter Share",
      "height=450,width=550,left=" +
        (window.innerWidth / 2 - 275) +
        ",top=" +
        (window.innerHeight / 2 - 225)
    );
  }
}

function shareToFacebook() {
  const currentUrl = encodeURIComponent(window.location.href);
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    window.open(`fb://share?link=${currentUrl}`, "_blank");
  } else {
    window.open(
      `https://www.facebook.com/sharer/sharer.php?u=${currentUrl}`,
      "Facebook Share",
      "height=450,width=550,left=" +
        (window.innerWidth / 2 - 275) +
        ",top=" +
        (window.innerHeight / 2 - 225)
    );
  }
}

function shareToMessenger() {
  const currentUrl = encodeURIComponent(window.location.href);
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    window.open(`fb-messenger://share?link=${currentUrl}`, "_blank");
  } else {
    window.open(
      `https://www.facebook.com/dialog/send?app_id=your_app_id&link=${currentUrl}`,
      "Messenger Share",
      "height=450,width=550,left=" +
        (window.innerWidth / 2 - 275) +
        ",top=" +
        (window.innerHeight / 2 - 225)
    );
  }
}

function shareToWhatsapp() {
  const currentUrl = encodeURIComponent(window.location.href);
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    window.open(`whatsapp://send?text=${currentUrl}`, "_blank");
  } else {
    window.open(
      `https://web.whatsapp.com/send?text=${currentUrl}`,
      "WhatsApp Share",
      "height=450,width=550,left=" +
        (window.innerWidth / 2 - 275) +
        ",top=" +
        (window.innerHeight / 2 - 225)
    );
  }
}

function shareToTelegram() {
  const currentUrl = encodeURIComponent(window.location.href);
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    window.open(`tg://msg_url?url=${currentUrl}`, "_blank");
  } else {
    window.open(
      `https://telegram.me/share/url?url=${currentUrl}`,
      "Telegram Share",
      "height=450,width=550,left=" +
        (window.innerWidth / 2 - 275) +
        ",top=" +
        (window.innerHeight / 2 - 225)
    );
  }
}

function shareToEmail() {
  const subject = encodeURIComponent(document.title);
  const body = encodeURIComponent(window.location.href);
  window.open(`mailto:?subject=${subject}&body=${body}`, '_blank');
}