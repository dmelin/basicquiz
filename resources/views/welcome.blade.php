<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz Game</title>
    @vite(['resources/js/app.js', 'resources/css/app.scss'])
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
</head>

<body>
    <div id="app">
        <button @click="newGame" class="new-game">New Game</button>

        <div v-if="victory" class="victory">
            <h2>Congratulations!</h2>
            <p>@{{ victoryMessage }}</p>
        </div>
        <div v-else-if="question" class="quiz">
            <div class="question">@{{ question.content }}</div>
            <div class="options">
                <button
                    v-for="option in question.options"
                    :key="option"
                    @click="submitAnswer(option)">
                    @{{ option }}
                </button>
            </div>
        </div>

        <div v-if="results" class="results">
            <p>Current streak: @{{ results.current_streak }}</p>
            <h4>Previous answers:</h4>
            <ul>
                <li v-for="answer in results.answers" :key="answer.question_id" :class="answer.correct ? 'correct' : 'wrong'">
                    <span>@{{ answer.question }}</span><br>
                    <span>
                        Your answer: @{{ answer.chosen_answer }}
                        <span v-if="!answer.correct"> (Correct: @{{ answer.correct_answer }})</span>
                    </span>
                </li>
            </ul>
        </div>
    </div>

    <script>
        const app = Vue.createApp({
            data() {
                return {
                    userToken: localStorage.getItem('user_token') || null,
                    question: null,
                    results: null,
                    victory: false,
                    victoryMessage: ''
                }
            },
            mounted() {
                if (!this.userToken) {
                    this.createToken();
                } else {
                    this.fetchNextQuestion();
                }
            },
            methods: {
                async createToken() {
                    const res = await fetch('/api/generate-token');
                    const data = await res.json();
                    this.userToken = data.token;
                    localStorage.setItem('user_token', this.userToken);
                    this.fetchNextQuestion();
                },
                async newGame() {
                    localStorage.removeItem('user_token');
                    this.userToken = null;
                    this.question = null;
                    this.results = null;
                    this.victory = false;
                    this.victoryMessage = '';
                    this.createToken();
                },
                async fetchNextQuestion() {
                    if (!this.userToken) return;
                    const res = await fetch(`/api/next-question/${this.userToken}`);
                    const data = await res.json();

                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    this.results = data.results;

                    if (data.victory) {
                        this.victory = true;
                        this.victoryMessage = data.message;
                        this.question = null;

                        return;
                    }

                    this.question = data.question;
                    this.results.answers.reverse(); // Reverse the order of answers for display
                },
                async submitAnswer(answer) {
                    if (!this.userToken || !this.question) return;
                    const res = await fetch(`/api/check-answer/${this.userToken}?question_id=${this.question.id}&answer=${answer}`);
                    const data = await res.json();

                    // Fetch next question and updated results
                    this.fetchNextQuestion();
                }
            }
        });
        app.mount('#app');
    </script>
</body>

</html>