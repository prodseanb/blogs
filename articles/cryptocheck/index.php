<?php 
$basedir = '../../';
include($basedir.'includes/header.php'); 
?> 

      <section>

      <div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
</span></code></pre></div></div>

<div style="width:100%;height:0;padding-bottom:100%;position:relative;"><iframe src="https://giphy.com/embed/l49JMVDvP8D38LHwI" width="100%" height="100%" style="position:absolute" frameBorder="0" class="giphy-embed" allowFullScreen></iframe></div><p><a href="https://giphy.com/gifs/money-cryptocurrency-bitcoin-l49JMVDvP8D38LHwI"></a></p>
<h1 id="ctflearn">How to Scrape Cryptocurrency Data in Python</h1>
<p>
Need to keep track of the latest cryptocurrency news and data? In this article, we will be learning how to scrape credible sites 
for real-time cryptocurrency data in Python using <a href="https://pypi.org/project/beautifulsoup4/">BeautifulSoup</a>, including the current price, market cap, 
24h trading volume, 24h low-high, price change, latest news, etc.
</p>





<pre><code>
[*] Date and time: 02/07/2021 00:39:33
[*] Coin: Bitcoin
[*] Abbrv: (BTC)
[*] Current price: $33,027.32
[*] Rank: Rank #1
[24h] Low: $32,811.23  ------------------  [24h] High: $34,447.84
[24h] Trading volume: $35,025,072,352
[24h] Price change: $-1,120.07
[*] Market dominance: 45.63%
[*] Circulating supply: 18,746,081.00 BTC
[*] Market cap: $619,132,732,901
[*] Latest news: Fake Covid Certificates, Stolen Vaccines Sold on Darkweb for Bitcoin 
[*] Source: https://www.coindesk.com/price/bitcoin
</code></pre>



<h3>Let's get started</h3>
<p>
For this project, we will need to install <a href="https://pypi.org/project/beautifulsoup4/">BeautifulSoup</a>, a Python library used
for the automation of web scraping.
<br/><br/>
Once you have configured a virtual environment (check out this <a href="https://docs.python.org/3/library/venv.html">documentation</a>), proceed to the installation of our required libraries.
<br/><br/>
Install BeautifulSoup:
</p>
<pre><code>pip3 install beautifulsoup4
</pre></code>
<p>Install <a href="https://pypi.org/project/requests/">Requests</a> for automating HTTP requests:</p>
<pre><code>pip3 install requests</pre></code>
<p>
We will be exploring <a href="https://coinmarketcap.com/">CoinMarketCap</a> and <a href="https://www.coindesk.com/">CoinDesk</a> for
real-time data. For the most part, we will be digging into the source code of both sites using our browser's <b>developer tools</b> to look
for the specific <b>elements</b> and <b>tags</b> we're going to work with.
</p>
<h3>Disclaimer</h3>
<p>
As much as I would like to update this site regularly, I may not be able to be informed quickly if sudden changes occur to both sites' source code.
The developers of both sites may decide to rename or revamp certain elements of the site, which could potentially affect the efficiency of this project.
</p>
<p><img src="https://user-images.githubusercontent.com/59718043/126219536-fa68be6c-68c6-4901-8d49-d67c6acc108e.jpg" alt="Goal Vectors by Vecteezy - https://www.vecteezy.com/free-vector/goal" /></p>


<br/><br/>
<h1>File Setup</h1>
<p>
First, we're going to create our project's directory. I'm going to call this project <b>cryptocheck</b>.
<br/><br/>
Inside cryptocheck, we're going to create two files, <code>run.py</code> and <code>banner.py</code>. <b>run.py</b> contains
our main code, while <b>banner.py</b> contains our usage documentation and program banner.
</p>
<pre><code><b>Directory tree:</b>
├── cryptocheck
│   ├── banner.py
│   └── run.py
</pre></code>
<p>
First, let's import all the necessary libraries and modules. 
</p>
<pre><code><b>File: run.py (cryptocheck/run.py)</b>
import sys
import requests # for creating web requests
from bs4 import BeautifulSoup
import sys
from datetime import datetime
import platform
import subprocess       
import banner as banner # Link to our banner.py
</pre></code>
<p>
Next, we're going to declare the necessary lists for each value that we want to scrape.
</p>

<pre><code>
news = []          # scrape latest news
coin = []          # scrape coin name
abbrv = []         # scrape abbrv
price = []         # scrape price
rank = []          # coin rank
low_high = []      # 24h low-high
supply = []        # circulating supply, volume, market cap
price_change = []  # scrape price change
</pre></code>

<p><img src="https://user-images.githubusercontent.com/59718043/126226665-ab80ac67-2a2e-48c9-8af9-9712e3dd7b9a.jpg" alt="Code Vectors by Vecteezy - https://www.vecteezy.com/free-vector/code" /></p>
<h3>Structure</h3>
<p>
<b>run.py</b> will essentially have three main components/modules: <code>get_news()</code>, <code>main()</code>, and <code>if __name__ == "__main__"</code>.
Note that the order of these modules in the file is important.
<br/><br/>
<b>get_news()</b> will be used for scraping the latest news from CoinDesk, <b>main()</b> contains our main blocks of code, and 
<b>if __name__ == "__main__"</b> is a boilerplate for starting the program.
</p>
<pre><code>
def get_news():
      pass

def main(*arguments):
      pass

if __name__ == "__main__":
      pass
</pre></code>
<p>
<b>banner.py</b> will have two functions: <code>head()</code> and <code>usage()</code>.
<br/><br/>
<b>head()</b> contains a custom banner that is executed when we start the program, while <b>usage()</b> contains the project
documentation.
</p>
<pre><code><b>File: banner.py (cryptocheck/banner.py)</b>
def head():
      pass

def usage():
      pass
</pre></code>

<br/><br/>
<h1>Fill in the modules</h1>
<p>
Going back to <b>run.py</b>, we're going to start working on our <code>if __name__ == "__main__"</code> block.
</p>

<pre><code><b>File: run.py (cryptocheck/run.py)</b>
if __name__ == "__main__":

    try:
        args = sys.argv
        request = requests.get(
            f'https://coinmarketcap.com/currencies/{sys.argv[1]}')
        if request.status_code == 200:  # check if url exists
            if platform.system() == "Windows":
                subprocess.call('cls', shell=True)
            else:
                subprocess.call('clear', shell=True)
            main(args)
        else:
            print('[!] Coin not found.')
            banner.usage()
    except IndexError:
        banner.usage()
    except UnboundLocalError:
        print(
            f"[!] No news found. Please check https://coinmarketcap.com/currencies/{sys.argv[1]}/")
</pre></code>
<p>
Here in this block of code, we set the file to accept the execution of additional arguments, verify the existence of the coin in
the database, and handle some logical errors. 

<br/><br/>
This line: 
<br/>
<code>requests = requests.get(f'https://coinmarketcap.com/currencies/{sys.argv[1]}')</code>
<br/>
connects us to the URL, which contains a value depending on the currency name argument <code>sys.argv[1]</code>. 
<br/><br/>
The next line checks the status code of this URL and verifies the existence of the currency. 
<br/><br/>
We also have to put in some exceptions for error handling. <code>IndexError()</code> catches bad arguments, and
<code>UnboundLocalError()</code> catches the process if the currency has no latest news.
</p>

<p><img src="https://user-images.githubusercontent.com/59718043/126245500-2f0c2f6a-4e73-48fb-9de2-6c77142b4bd6.png" alt="Error Vectors by Vecteezy - https://www.vecteezy.com/free-vector/error" /></p>

<br/><br/>
<h1>Let's start scraping</h1>
<p>
We're going to remain in <b>run.py</b> for a while, only this time we're working on <code>main()</code>. 
</p>
<pre><code>
def main(*arguments):
    URL = f"https://coinmarketcap.com/currencies/{sys.argv[1]}"
    page = requests.get(URL)
    soup = BeautifulSoup(page.text, 'html.parser')
</pre></code>
<p>
The first block of this function requests data from the URL provided, and parses it in HTML.
</p>
<p>This line displays the name of the currency:</p>
<pre><code>
    print(f'[*] Selected coin: {sys.argv[1]}')
</pre></code>
<p>This next block loops through every match of a specific class within the <code>h2</code> element,
gets rid of an unwanted <code>small</code> tag, takes the text element of <code>h2</code>, and appends it to our 
<code>coin[]</code> list.
</p>
<pre><code>
    for h2 in soup.findAll('h2', class_="sc-1q9q90x-0 iYFMbU h1___3QSYG"):
            small = h2.find('small')  # unwanted small tag
            small.extract()
            coin_name = h2.get_text()
            coin.append(coin_name)
</pre></code>
<p>
This loop searches for the currency abbreviations:
</p>
<pre><code>
    for h1 in soup.findAll('h1', class_="priceHeading___2GB9O"):
            for small in h1.findAll('small'):
                abbrv_val = small.get_text()
                abbrv.append(abbrv_val)
</pre></code>

<p><img src="https://user-images.githubusercontent.com/59718043/126248286-9a1ab0d1-9c72-4c4c-be61-d88747461681.jpg" alt="Human Vectors by Vecteezy - https://www.vecteezy.com/free-vector/human" /></p>

<p>
The next few blocks do the same thing for the price, rank, 24h low-high, supply, and price change.
</p>

<pre><code>
    for p in soup.findAll('div', class_="priceValue___11gHJ"):
        price_val = p.get_text()
        price.append(price_val)
        # print(price)

    for rank in soup.findAll('div', class_="namePill___3p_Ii namePillPrimary___2-GWA"):
        rank_val = rank.get_text()
        rank.append(rank_val)

    for span in soup.findAll('span', class_="highLowValue___GfyK7"):
        val = span.get_text()
        low_high.append(val)

    for div in soup.findAll('div', class_="statsValue___2iaoZ"):
        val = div.get_text()
        supply.append(val)

    for table in soup.findAll('table'):
        for td in table.findAll('td'):
            for span in td.findAll('span'):
                val = span.get_text()
                price_change.append(val)
</pre></code>

<p>The next line, after all the loops, calls the <code>head()</code> function from <b>banner.py</b>, which contains our program banner.</p>
<pre><code>
    banner.head()
</pre></code>
<p>
We need to display the date and time of each search query. This block finds the current date and time and stores it in a particular 
format.
</p>
<pre><code>
    now = datetime.now()
    dt_string = now.strftime("%d/%m/%Y %H:%M:%S")
    print(f"[*] Date and time: {dt_string}")
</pre></code>

<h3>Output</h3>
<pre><code>
    if len(sys.argv) == 2 or '-a' in sys.argv[1:]:
            for index, (val1, val2, val3, val4) in \
                    enumerate(zip(coin, abbrv, price, rank)):

                print(
                    f"[*] Coin: {val1}\n[*] Abbrv: {val2}\n[*] Current price: {val3}\n[*] Rank: {val4}")

            print("[24h] Low: " + low_high[0] +
                "  ------------------  [24h] High: " + low_high[1])
            print("[24h] Trading volume: " + supply[2])
            print("[24h] Price change: " + price_change[0])
            print("[*] Market dominance: " + price_change[6])
            print("[*] Circulating supply: " + supply[4])
            print("[*] Market cap: " + supply[0])
</pre></code>
<p>
If the length of <code>sys.argv</code> is less than 2 positional arguments, or if the option <code>-a</code> exists in 
<code>sys.argv[1:]</code>, (<b>[1:]</b> means the positions in query are after the 1st positional argument.) display all the information
about the currency.
<br/><br/>
Continuing within this if-else statement, we're going to nest another if-else procedure that checks for the availability of the 
currency's latest news.
</p>
<pre><code>
            get_news()
            if len(news) == 0:
                print(
                    f'[!] News not found. Please check https://coinmarketcap.com/currencies/{sys.argv[1]}/')
            else:
                print("[*] Latest news: " + news[0] +
                    f"\n[*] Source: https://www.coindesk.com/price/{sys.argv[1]}")
</pre></code>
<p>
The next few if-blocks, after our news if-else block, display the output in query based on the user input. 
</p>

<pre><code>
    if '-p' in sys.argv[1:] or '--price' in sys.argv:
        print("[*] Current price: " + price[0])
    if '-c' in sys.argv[1:] or '--price-change' in sys.argv[1:]:
        print("[24h] Price change: " + price_change[0])
    if '-T' in sys.argv[1:] or '--volume' in sys.argv[1:]:
        print("[24h] Trading volume: " + supply[2])
    if '-K' in sys.argv[1:] or '--low-high' in sys.argv[1:]:
        print("[24h] Low: " + low_high[0] +
              "  ------------------  [24h] High: " + low_high[1])
    if '-d' in sys.argv[1:] or '--dominance' in sys.argv[1:]:
        print("[*] Market dominance: " + price_change[6])
    if '-s' in sys.argv[1:] or '--supply' in sys.argv[1:]:
        print("[*] Circulating supply: " + supply[4])
    if '-M' in sys.argv[1:] or '--market-cap' in sys.argv[1:]:
        get_news()
        print("[*] Market cap: " + supply[0])
    if '-n' in sys.argv[1:] or '--news' in sys.argv[1:]:
        get_news()
        print("[*] Latest news: " + news[0] +
              f"\n[*] Source: https://www.coindesk.com/price/{sys.argv[1]}")
</pre></code>


<h3>banner.py</h3>
<p>
In this file, let your creative juices flow and create a banner and documentation for the program.
</p>
<pre><code><b>File: banner.py (cryptocheck/banner.py)</b>
def head():
        # insert your own banner here
        print('Cryptocheck banner')

def usage():
        # insert you own usage documentation here
        print('usage')
</pre></code>
<h1>Examples</h1>
<p>
Try executing these examples:
<br/>
<code>python3 run.py dogecoin -a</code>
<br/>
<code>python3 run.py bitcoin --news -p</code>
<br/>
<code>python3 run.py cardano -M</code>
</p>
<p>
For the full code, check out cryptocheck's <a href="https://github.com/prodseanb/cryptocheck">repository</a>.
</p>
<br/><br/>
<h1>Sources</h1>

<table class="table table-striped table-bordered">
<thead>
<tr>
<th>Subject</th>
<th>Link</th>
</tr>
</thead>
<tbody>
<tr>
<td>BeautifulSoup</td>
<td><a href="https://pypi.org/project/beautifulsoup4/">pypi</a></td>
</tr>
<tr>
<td>Virtual Environment Python Docs</td>
<td><a href="https://docs.python.org/3/library/venv.html">Python</a></td>
</tr>
<tr>
<td>Requests</td>
<td><a href="https://pypi.org/project/requests/">pypi</a></td>
</tr>
<tr>
<td>CoinMarketCap</td>
<td><a href="https://coinmarketcap.com/">Site</a></td>
</tr>
<tr>
<td>CoinDesk</td>
<td><a href="https://www.coindesk.com/">Site</a></td>
</tr>

</tbody>
</table>

<table class="table table-striped table-bordered">
<thead>
<tr>
<th>Media</th>
</tr>
</thead>
<tbody>
<tr>
<td><a href="https://giphy.com/gifs/money-cryptocurrency-bitcoin-l49JMVDvP8D38LHwI">Money cryptocurrency GIF by Amy Ciavolino via GIPHY</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/goal">Goal Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/code">Code Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/human">Human Vectors by Vecteezy</a></td>
</tr>

</tbody>
</table>

<div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
## @Date: Jul 19 2021
</span></code></pre></div></div>

<div class="page-nav">
<a href="/index.php" class="previous round">&#8249;Home</a>
<!--<a href="#" class="next round">Next&#8250;</a>-->
</div>

      </section>
<?php 
$basedir = '../../';
include($basedir.'includes/footer.php'); 
?> 
