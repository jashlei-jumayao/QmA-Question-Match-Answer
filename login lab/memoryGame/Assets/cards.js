var errors = 0;

var cardSet;
var board = [];
var rows = 4;
var columns = 5;

var card1Selected;
var card2Selected;
var audio = new Audio('Audio/card1.ogg');

window.onload = () => {
    shuffleCards();
    startGame();
}

function shuffleCards() {
    cardSet = [];

    // Split the QnA pairs into individual cards, but tag them with the same pairId
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
            let cardData = cardSet.pop(); // This is now an object {src, pairId}
            row.push(cardData);

            let card = document.createElement("img");
            card.id = r.toString() + "-" + c.toString();
            card.src = cardData.src + ".png";
            card.classList.add("card");

            // Attach the pairId directly to the HTML element for easy comparison later
            card.dataset.pairId = cardData.pairId;

            card.addEventListener("click", selectCard);
            card.addEventListener("click", playSound);
            document.getElementById("board").append(card);
        }
        board.push(row);
    }
    setTimeout(hideCards, 1000);
}

function hideCards() {
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < columns; c++) {
            let card = document.getElementById(r.toString() + "-" + c.toString());
            card.src = "back.png";
        }
    }
}

function selectCard() {
    if (this.src.includes("back")) {
        let coords = this.id.split("-");
        let r = parseInt(coords[0]);
        let c = parseInt(coords[1]);

        if (!card1Selected) {
            card1Selected = this;
            // board[r][c] is now an object, so we access .src
            card1Selected.src = board[r][c].src + ".png";
        } else if (!card2Selected && this != card1Selected) {
            card2Selected = this;
            card2Selected.src = board[r][c].src + ".png";
            setTimeout(update, 1000);
        }
    }
}

function update() {
    // Compare the hidden dataset pairIds instead of the image source names
    if (card1Selected.dataset.pairId != card2Selected.dataset.pairId) {
        card1Selected.src = "back.png";
        card2Selected.src = "back.png";
        errors += 1;
        document.getElementById("errors").innerText = errors;
    }
    card1Selected = null;
    card2Selected = null;
}

function playSound() {
    audio.play();
}

function flipCardAnimation(cardElement, newSrc) {
    cardElement.classList.add("is-flipping");
    setTimeout(() => {
        cardElement.src = newSrc;
        cardElement.classList.remove("is-flipping");
    }, 150); // Matches half of the 0.15s transition
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

function hideCards() {
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < columns; c++) {
            let card = document.getElementById(r.toString() + "-" + c.toString());
            flipCardAnimation(card, "back.png");
        }
    }
}

function update() {
    if (card1Selected.dataset.pairId != card2Selected.dataset.pairId) {
        flipCardAnimation(card1Selected, "back.png");
        flipCardAnimation(card2Selected, "back.png");
        errors += 1;
        document.getElementById("errors").innerText = errors;
    }
    card1Selected = null;
    card2Selected = null;
    if (matchedPairs >= dynamicCardList.length) {
        finishGame();
    }
}

// LEADERBOARD ADDITIONS

var matchedPairs = 0;
var gameStartedAt = null;
var scoreSubmitted = false;

// Start the timer after the cards are hidden
var originalHideCards = hideCards;

hideCards = function() {
    originalHideCards();
    gameStartedAt = Date.now();
};

// Override update to track matched pairs and game completion
function update() {
    if (card1Selected.dataset.pairId != card2Selected.dataset.pairId) {
        flipCardAnimation(card1Selected, "back.png");
        flipCardAnimation(card2Selected, "back.png");

        errors += 1;

        document.getElementById("errors").innerText = errors;
    } else {
        matchedPairs++;
        document.getElementById("gameStatus").innerText =
            "Matched pairs: " + matchedPairs + " / " + dynamicCardList.length;
    }

    card1Selected = null;
    card2Selected = null;

    // Check if all pairs have been matched
    if (matchedPairs >= dynamicCardList.length) {
        finishGame();
    }
}

// Save completed game
function finishGame() {
    if (scoreSubmitted) {
        return;
    }

    scoreSubmitted = true;

    var completionSeconds = Math.floor(
        (Date.now() - gameStartedAt) / 1000
    );

    document.getElementById("gameStatus").innerText =
        "Game completed! Saving your score...";

    fetch("save_score.php", {
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
        if (data.success) {
            document.getElementById("gameStatus").innerText =
                "Game completed! Your score has been saved.";
        } else {
            document.getElementById("gameStatus").innerText =
                "Game completed, but your score could not be saved.";
        }
    })
    .catch(error => {
        console.error(error);

        document.getElementById("gameStatus").innerText =
            "Unable to save score. Please try again.";
    });
}