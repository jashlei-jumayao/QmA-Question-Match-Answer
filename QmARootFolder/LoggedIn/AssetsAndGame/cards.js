var errors = 0;
var cardSet;
var board = [];
var rows = 4;
var columns = 5;

var card1Selected;
var card2Selected;
var audio = new Audio('Audio/card1.ogg');

// Leaderboard & game state tracking
var matchedPairs = 0;
var gameStartedAt = null;
var scoreSubmitted = false;

window.onload = () => {
    shuffleCards();
    startGame();
};

function shuffleCards() {
    cardSet = [];

    // Split the QnA pairs into individual cards, tagged with the same pairId
    dynamicCardList.forEach(pair => {
        cardSet.push({ src: pair.q, pairId: pair.id });
        cardSet.push({ src: pair.a, pairId: pair.id });
    });

    // Shuffle the deck of objects
    for (let i = 0; i < cardSet.length; i++) {
        let j = Math.floor(Math.random() * cardSet.length);
        let temp = cardSet[i];
        cardSet[i] = cardSet[j];
        cardSet[j] = temp;
    }
}

function startGame() {
    for (let r = 0; r < rows; r++) {
        let row = [];
        for (let c = 0; c < columns; c++) {
            let cardData = cardSet.pop();
            row.push(cardData);

            let card = document.createElement("img");
            card.id = r.toString() + "-" + c.toString();
            card.src = cardData.src + ".png";
            card.classList.add("card");
            card.dataset.pairId = cardData.pairId;

            card.addEventListener("click", selectCard);
            card.addEventListener("click", playSound);
            document.getElementById("board").append(card);
        }
        board.push(row);
    }
    setTimeout(hideCards, 1000);
}

function flipCardAnimation(cardElement, newSrc) {
    cardElement.classList.add("is-flipping");
    setTimeout(() => {
        cardElement.src = newSrc;
        cardElement.classList.remove("is-flipping");
    }, 150);
}

function hideCards() {
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < columns; c++) {
            let card = document.getElementById(r.toString() + "-" + c.toString());
            flipCardAnimation(card, "back.png");
        }
    }
    gameStartedAt = Date.now();
}

function selectCard() {
    if (this.src.includes("back") && !this.classList.contains("is-flipping")) {
        let coords = this.id.split("-");
        let r = parseInt(coords[0]);
        let c = parseInt(coords[1]);

        if (!card1Selected) {
            card1Selected = this;
            flipCardAnimation(card1Selected, board[r][c].src + ".png");
        } else if (!card2Selected && this != card1Selected) {
            card2Selected = this;
            flipCardAnimation(card2Selected, board[r][c].src + ".png");
            setTimeout(update, 1000);
        }
    }
}

function update() {
    if (card1Selected.dataset.pairId != card2Selected.dataset.pairId) {
        flipCardAnimation(card1Selected, "back.png");
        flipCardAnimation(card2Selected, "back.png");

        errors += 1;
        document.getElementById("errors").innerText = errors;
    } else {
        matchedPairs++;
        const statusEl = document.getElementById("gameStatus");
        if (statusEl) {
            statusEl.innerText = "Matched pairs: " + matchedPairs + " / " + dynamicCardList.length;
        }
    }

    card1Selected = null;
    card2Selected = null;

    if (matchedPairs >= dynamicCardList.length) {
        finishGame();
    }
}

function playSound() {
    audio.play();
}

function finishGame() {
    if (scoreSubmitted) {
        return;
    }

    scoreSubmitted = true;

    var completionSeconds = Math.floor((Date.now() - gameStartedAt) / 1000);
    const statusEl = document.getElementById("gameStatus");
    if (statusEl) {
        statusEl.innerText = "Game completed! Saving your score...";
    }

    fetch("../save_score.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            errors: errors,
            completion_seconds: completionSeconds
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Failed to save score");
        }
        return response.json();
    })
    .then(data => {
        if (statusEl) {
            statusEl.innerText = data.success
                ? "Game completed! Your score has been saved."
                : "Game completed, but your score could not be saved.";
        }
    })
    .catch(error => {
        console.error(error);
        if (statusEl) {
            statusEl.innerText = "Unable to save score. Please try again.";
        }
    });
}