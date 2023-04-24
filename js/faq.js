const questions = document.querySelectorAll(".question");
let currentOpenAnswer;

questions.forEach((question) => {
    question.addEventListener("click", function () {
        const answer = this.nextElementSibling;
        const plusSign = this.querySelector(".fa-plus");

        if (answer === currentOpenAnswer) {
            answer.style.display = "none";
            plusSign.classList.remove("open");
            currentOpenAnswer = null;
        } else {
            if (currentOpenAnswer) {
                currentOpenAnswer.style.display = "none";
                currentOpenAnswer.previousElementSibling.querySelector(".fa-plus").classList.remove("open");
            }

            answer.style.display = "block";
            plusSign.classList.add("open");
            currentOpenAnswer = answer;
        }
    });
});