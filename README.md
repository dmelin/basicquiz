A very simple quiz game.

A random token is requested from the API and stored in the client local storage. Every request for next question, answers requires this token.

This simple game uses a 5 character alphanumeric token.

On each next question request the results (streak and history) for the provided token is also returned.

If the player has a streak of 10 the game ends.

If the backend runs out of questions it fetches a new one from NumbersAPI, turns the statement into a question, creates 3 false options (that somewhat looks like the correct answer).

The correct answer and false options are mixed together, randomized and shared with the client. Hence it is impossible to cheat by checking requests to see which answer is correct.

Good luck!

(my best streak, without looking in the database is 3)