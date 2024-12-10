@extends('layouts.page', [
    'url'         => 'https://eufirecalc.com/fire',
    'title'       => 'EUFireCalc - Fire Simulator',
    'description' => 'EUFireCalc is a collection of tools around Financial Independence / Retire Early (FIRE), with a focus on Europe.',
    'activeNavbarSection' => 'Fire'
])

@section('content')
    <div class="section simulator-explain-section">
        <div class="container is-max-desktop">
            <div class="has-text-centered">
                <h1 class="title main-colored-text simulator-colored-text">Fire Simulator</h1>
                <p>This simulator will backtest your FIRE plan for you. For every year starting from the starting year it will play out your scenario, simulating your monthly contributions and withdrawals as the stock price goes up and down.</p>
            </div>
        </div>
        <form class="container fire-form-section">
            <div class="columns is-desktop">
                <div class="column settings-generic">
                    <h1 class="settings-h1">General Settings</h1>
                    <div class="columns is-1 is-multiline">
                        <div class="column field medium">
                            <label class="label">Current Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field small">
                            <label class="label">End Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="90">
                            </div>
                        </div>
                        <div class="column field medium">
                            <label class="label">Start Amount</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="50,000">
                            </div>
                        </div>
                        <div class="column field large">
                            <label class="label">Use Real Inflation?</label>
                            <div class="control">
                                <div class="select">
                                <select class="input">
                                    <option>Yes</option>
                                    <option>No</option>
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="column field small">
                            <label class="label">Inflation</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="2.5%">
                            </div>
                        </div>
                        <div class="column field small">
                            <label class="label">Comparison</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="9.5%">
                            </div>
                        </div>
                    </div>
                    <div class="columns is-1 is-multiline">
                        <div class="column field large">
                            <label class="label">Tax System</label>
                            <div class="control">
                                <div class="select">
                                <select class="input">
                                    <option>None</option>
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="column field medium">
                            <label class="label">Data Since</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="1871">
                            </div>
                        </div>
                        <div class="column field large">
                            <label class="label">Cut Off Graph At</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="2,500,000">
                            </div>
                        </div>
                        <div class="column field huge">
                            <label class="label">Number Display</label>
                            <div class="control">
                                <div class="select">
                                <select class="input">
                                    <option>Inflation Adjusted</option>
                                    <option>Nominal</option>
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="column field large">
                            <label class="label">Graph Display</label>
                            <div class="control">
                                <div class="select">
                                <select class="input">
                                    <option>Area</option>
                                    <option>Lines</option>
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns is-desktop">
                <div class="column settings-deposits">
                    <h1 class="settings-h1">Contribution Settings</h1>
                    <div class="columns is-1 is-multiline">
                        <div class="column field small">
                            <label class="label">Start Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field small">
                            <label class="label">End Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field huge">
                            <label class="label">Amount</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input class="input" type="text" inputmode="numeric" placeholder="€2,000">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option>One-Off</option>
                                            <option selected>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="column field x-large">
                            <label class="label">Increase By</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input disabled class="input" type="text" inputmode="numeric" placeholder="0%">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option selected>Inflation</option>
                                            <option>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="columns is-1 is-multiline">
                        <div class="column field small">
                            <label class="label">Start Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field small">
                            <label class="label">End Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field huge">
                            <label class="label">Amount</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input class="input" type="text" inputmode="numeric" placeholder="€2,000">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option>One-Off</option>
                                            <option selected>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="column field x-large">
                            <label class="label">Increase By</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input disabled class="input" type="text" inputmode="numeric" placeholder="0%">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option selected>Inflation</option>
                                            <option>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="columns is-1 is-multiline">
                        <div class="column field small">
                            <label class="label">Start Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field small">
                            <label class="label">End Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field huge">
                            <label class="label">Amount</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input class="input" type="text" inputmode="numeric" placeholder="€2,000">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option>One-Off</option>
                                            <option selected>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="column field x-large">
                            <label class="label">Increase By</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input disabled class="input" type="text" inputmode="numeric" placeholder="0%">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option selected>Inflation</option>
                                            <option>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column settings-withdrawals">
                    <h1 class="settings-h1">Withdrawal Settings</h1>
                    <div class="columns is-1 is-multiline">
                        <div class="column field small">
                            <label class="label">Start Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field small">
                            <label class="label">End Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field huge">
                            <label class="label">Amount</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input class="input" type="text" inputmode="numeric" placeholder="€2,000">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option>One-Off</option>
                                            <option selected>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="column field x-large">
                            <label class="label">Increase By</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input disabled class="input" type="text" inputmode="numeric" placeholder="0%">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option selected>Inflation</option>
                                            <option>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="columns is-1 is-multiline">
                        <div class="column field small">
                            <label class="label">Start Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field small">
                            <label class="label">End Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field huge">
                            <label class="label">Amount</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input class="input" type="text" inputmode="numeric" placeholder="€2,000">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option>One-Off</option>
                                            <option selected>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="column field x-large">
                            <label class="label">Increase By</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input disabled class="input" type="text" inputmode="numeric" placeholder="0%">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option selected>Inflation</option>
                                            <option>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="columns is-1 is-multiline">
                        <div class="column field small">
                            <label class="label">Start Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field small">
                            <label class="label">End Age</label>
                            <div class="control">
                                <input class="input" type="text" inputmode="numeric" placeholder="30">
                            </div>
                        </div>
                        <div class="column field huge">
                            <label class="label">Amount</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input class="input" type="text" inputmode="numeric" placeholder="€2,000">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option>One-Off</option>
                                            <option selected>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="column field x-large">
                            <label class="label">Increase By</label>
                            <div class="field has-addons" style="margin-top: 0">
                                <p class="control">
                                    <input disabled class="input" type="text" inputmode="numeric" placeholder="0%">
                                </p>
                                <p class="control frequency-control">
                                    <span class="select">
                                        <select>
                                            <option selected>Inflation</option>
                                            <option>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="section">

    </div>
@endsection
