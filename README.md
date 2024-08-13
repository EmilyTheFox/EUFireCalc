# Europe FI/RE Calculator

## For the devs
This app is containerized and doesn't require any complex setup. Just make sure you have Docker installed.

Just simply run `docker-compose up --build -d`

The project should now be running at `localhost:8000`

## Foreword
This is just a simple quick project for the sake of having ANY code at all that can be publically shown as all of my other work can't be shown for \
A. Legal reasons \
B. It being many years old and not representing my skill-level or \
C. Being garbage code I slapped together in a couple hours for myself as the only user.

This site fell into category C itself. It used to just be a simple html/css/js front-end with no back-end at all and it was garbage code with no tests or anything. 

Rewriting it into a project that actually has a back-end is cool and all but in the end this isn't really a site that'd need a back-end so it still feels slightly weird to do anyway and it will be quite the small project.

## The Project

This site is a small tool to calculate and visualize historical odds of succeeding at FI/RE. (as well as some other little calculators surrounding FI/RE)

### What is FI/RE?
FI/RE (often called Fire 🔥) stands for "Financial Independence / Retire Early" and refers to the mindset of saving up enough money early on in life so that you no longer rely on your job or other people to afford your basic needs in life (Financial Independence).\
The main motivation for this for many people is the desire to quit working entirely or scale back to a part-time job way before their national retirement age (Retire Early).

The main way FI/RE is achieved is through investing. The theory is that if you have enough money, you can live of the returns of your investment portfolio (made up of low risk Index Funds/ETFs and Bonds).

### How does this site help with FI/RE?
There are 2 main questions when it comes to FI/RE.
1. How much money do I need to afford my cost of living from stock returns? (this is often called your "number")
2. How much longer do I have to work to get to my number?

These questions however don't have concrete answers, rather they can be answered with "it depends".\
What does it depend on? The stock market.

If we are relying on the stock market to generate enough income to afford our cost of living, then the height of our number (how much money we need initially to be able to live off the returns) depends on how the stock market is doing.

It's easy to say "on average the S&P500 returns 11%/yr". 
But that doesn't actually tell us much, because of the importance of Sequence of Returns.

For example let's say we invest 10k/yr and the market returns an average of 11%/yr

In the first case our market returns +30% -20% +23%\
Invest 10k -> +30% = 13k -> Invest 10k = 23k -> -20% = 18.4k -> Invest 10k = 28.4k -> +23% = **34.93k**

In the second case our market returns +23% +30% -20%\
Invest 10k -> +23% = 12.3k -> Invest 10k = 22.3k -> +30% = 28.99k -> Invest 10k = 38.99k -> -20% = **31.19k**

In just 3 years, while both are averaging 11%/yr, the difference in the final number is already quite big.\
If we continue this for a lifetime of investing and withdrawing, you can start to see why a statement like "11%/yr" isn't helpful.

**So, to get back to how this site helps**

With this site you can visualize how the stock market has historically performed for your contribution and withdrawal strategy. And what your chance of having never ran out of money is, based on when in the sequence you started. On top of that it will let you see how other countries compare and other bits of info like how working shorter or longer would affect how much you can spend per year.

This all together will help get a much more accurate answer to the questions mentioned above.

Of course this is all still just an estimate. Past performance is not a guarantee for the future. But it can be a pretty good indicator and give hope to people dreading having to work until they're 70 or older.

# Disclaimer
This project is not aiming to give financial advice. Investing in the stock market is risky and you can lose some or all of your money.

This project is purely for nerding out and getting more accurate estimates.\
**Past returns are not indicative of future performance**\
Even if historically your strategy would've had a 100% success rate, the stock market is always changing, always getting more unpredictable and nothing is stopping us from having a worse time than we've ever seen in history. Yes there are many more regulations since events like the great depression but those really wont save you in the case of a major crash.