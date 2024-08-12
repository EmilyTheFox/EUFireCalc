# API Plans

Just writing this out to get some thinking out of the way
dont know how much Ill actually stick to this

# GET /v1/taxsystems

Purpose:
- Color a map of Europe with the different Capital Gains / Wealth tax amounts
- Give front-end a list of what countries their tax systems are supported

(Main goal for now is Netherlands, Belgium, Germany, Denmark, Austria, Switzerland, France, Spain, Portugal, Italy,
Then when those are all there, consider Norway, Sweden, Finland)

Request:
*No body*

Response:
{
    "data": {
        "countries": [
            {
                "name": "Austria",
                "capitalGains": 27.5,
                "wealthTax": 0,
                "specialRules": "In Austria you have to pre-pay taxes of differing amounts based on what kind of index fund you're in. Generally speaking you want a fund that doesn't constantly buy and sell shares like SWAP-based funds. Something like VWCE would do just fine but please get actual financial advice if you're in Austria."
            },
            {
                "name": "Netherlands",
                "capitalGains": 0,
                "wealthTax": 2.1744,
                "specialRules": "First 57,000 euros of savings are tax-free. The Netherlands is planning to move to a Capital Gains focused tax system rather than wealth-taxing investments, however wealth tax on cash savings will likely stay."
            },
            {
                "name": "Germany",
                "capitalGains": 25,
                "wealthTax": 0,
                "specialRules": "In germany there is \"Vorabpauschale\" which means they tax your investment amount every year based on how much income it would've given you if you simply did risk-free investments. For 2024 that is '2.29% * 0.7 Multiplier * 25% Tax * Your Amount'"
            },
            ...
        ]
    }
}

# GET /v1/inflation

Purpose:
Front-end will have to parse this and both plot a bar chart 
as well as the progression of 1,000 euro over 1960-2024 for illustration of what inflation is like (and note the average)
as well as plot the stock returns percentages between these periods to possibly highlight any trends in how these move

Request:
*No Body*

Response:
*info from https://www.macrotrends.net/global-metrics/countries/EMU/euro-area/inflation-rate-cpi * 
{
    "data": {
        "yearlyPercentages": {
            "1960": 1.74,
            "1961": 1.87,
            ...
            "2022": 8.47
        }
    }
}


# GET /v1/returns

Purpose:
- Let front-end plot returns of 1,000 eur for the selected years

Request:
*No Body*

Response:
*info from https://www.officialdata.org/us/stocks/s-p-500/1871?amount=100&endYear=2024#data *
{
    "data": {
        "monthlyReturns": {
            "1871-1": 1.84,
            "1871-2": 2.93,
            ...
            "2024-5": 5.34 
        },
        "yearlyReturns": {
            "1871": ??.?,
            "1872": ??.?,
            ...
            "2023": ??.?
            
            We will want to automate going through the monthly data to get the yearly data sometime. Just write some shit JavaScript idk.
        }
    }
}

# POST /v1/simulate

Purpose:
- Get back several interesting graphs of how money would've developed such assss
1. The Area or Individual Runs of our money based on the paramaters 
2. Find what year was the median and also run that against all other countries their taxes so there is a nice comparison of what country is best (I'd also like their success rates but that'd require running everything 20 times over sooo- no ty?)
3. Show the median year against working 2 years less, 1 year less, 1 year more & 2 years more. to show a bit of spread

Request:
{
    "startAge": number, // 22
    "coastAge": number, // 30
    "fireAge": number, // 35
    "endAge": number, // 90
    "realInflation": boolean, // true
    "staticInflation": number, // 2.5%, only required if realInflation is false
    "flatReturns": number, // 10%, nullable, backend should just ignore it if its 0 tbh
    "taxSystem": String, // 'Austria',
    "displayType": String, // 'Area' or 'Runs',
    "dataSince": number, // 1871 or later,
    "startWorth": number, // 60000,
    "contributions": [
        {
            "startAge": number,
            "endAge": number,
            "amount": number,
            "frequency": String, // 'Monthly', 'Quarterly', 'Yearly', 'One-Off'
            "increaseAmount": number,
            "increaseFrequency": String, // 'Monthly', 'Quarterly', 'Yearly', 'Match Inflation', 'Never'
        },
        ...
    ],
    "withdrawals": [
        {
            "startAge": number,
            "endAge": number,
            "amount": number,
            "frequency": String, // 'Monthly', 'Quarterly', 'Yearly', 'One-Off'
            "increaseAmount": number,
            "increaseFrequency": String, // 'Monthly', 'Quarterly', 'Yearly', 'Match Inflation', 'Never'
        },
        ...
    ]
}

Response:
*Uses data from /v1/returns and from /v1/inflation (use swiss inflation before 1960? due to it being neutral in both WorldWars? https://lu.app.box.com/s/swz11hw2t2hffmejljm9wjsif02rpwcl )

{
    "data": {
        "runs": [
            [
                100,
                101,
                103
            ],
            [
                100,
                101.2,
                103.3
            ],
            ...
            [
                100,
                102.5,
                104.3
            ]
        ]
        "countryComparisons": [
            {
                "country": "Austria",
                "yearlyData": [
                    100,
                    101,
                    103,
                    105.5
                    ...
                ]
            },
            {
                "country": "Germany",
                "yearlyData": [
                    ...
                ]
            }
        ],
        "workingYearsComparison": {
            "minusTwo": [
                100,
                101,
                ...
            ],
            "minusOne": [
                100,
                101,
                ...
            ],
            "noChange": [
                100,
                101,
                ...
            ],
            "plusOne": [
                100,
                101,
                ...
            ],
            "plusTwo": [
                100,
                101,
                ...
            ]
        }
    }
}