<?php 
$basedir = '../../';
include($basedir.'includes/header.php'); 
?> 

      <section>

      <div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
</span></code></pre></div></div>

<div style="width:100%;height:0;padding-bottom:100%;position:relative;"><iframe src="https://giphy.com/embed/l1J9qemh1La8b0Rag" width="100%" height="100%" style="position:absolute" frameBorder="0" class="giphy-embed" allowFullScreen></iframe></div><p><a href="https://giphy.com/gifs/vintage-computer-old-l1J9qemh1La8b0Rag"></a></p>
<h1 id="ctflearn">Microsoft Revealed Another Windows Print Spooler Service RCE Vulnerability</h1>
<p>
On August 11, 2021, Microsoft revealed yet another Windows 10 Print Spooler remote code execution vulnerability. This flaw is the latest bug in the list
of Print Spooler service vulnerabilities known as the <a href="https://www.redscan.com/news/printnightmare-security-advisory/">PrintNightmare</a>.

</p>

<p>
Identified as <a href="https://msrc.microsoft.com/update-guide/vulnerability/CVE-2021-36958">CVE-2021-36958</a>, this bug
allows an attacker to run arbitrary code with system privileges. According to Microsoft's executive summary, 
<i>"an attacker could install programs; view, change, or delete data; or create new accounts with full user rights."</i>
<br/><br/>
A previous related CVE entry, <a href="https://nvd.nist.gov/vuln/detail/CVE-2021-34481">CVE-2021-34481</a>, improperly performs
privileged file operations. Microsoft has released security patches to address this vulnerability on August 10, the day before
CVE-2021-36958 was revealed.

</p>

<br/><br/>
<p><img src="https://user-images.githubusercontent.com/59718043/129310558-0cf0a531-b0f1-41aa-b939-c0e6e6a1355d.jpg" /></p>
<p>
Over the past few months, the issues affecting this service have been exposed consecutively as a result of researchers finding 
different methods to attack its flaws. <a href="https://twitter.com/offenseindepth/status/1425586599639887874">Accenture Security Researcher, Victor Mata</a>, 
says he reported the issues back in December 2020.
</p>

<p>
Researcher Zhipeng Huo, mentioned on a <a href ="https://twitter.com/R3dF09/status/1410533078112432131?ref_src=twsrc%5Etfw%7Ctwcamp%5Etweetembed%7Ctwterm%5E1410533078112432131%7Ctwgr%5E%7Ctwcon%5Es1_&ref_url=https%3A%2F%2Fthestack.technology%2Fprintnightmare-poc-leaked%2F">Twitter thread</a>  
that Microsoft took one year to fix a bug related to this string of PrintNightmare events.
</p>

<p>
According to Zhipeng, the vulnerability was reported back in July 2020, the case was tracked as <b>MSRC Case 60036</b>.
</p>

<p>
Microsoft responded in August 2020, asking Zhipeng to keep matters confidential as they are working to address this issue.
For consecutive months, Microsoft kept Zhipeng updated, stating that they were <b>still working on a fix</b>.
</p>


<p>
In February 2021, Microsoft discovered another related issue and mentioned that they were planning to release a security update on June 8.
Finally, on June 8, Microsoft released a security patch, but the story obviously does not end here. 

<br/><br/>
As a result of this consecutive string of events, many security researchers and online forums mock the service, bringing more 
emphasis to its vulnerablity.
</p>


<br/><br/>
<h1>Mitigation</h1>
<p><img src="https://user-images.githubusercontent.com/59718043/129311817-b1f90587-c5f0-467a-975e-a640b7570bbe.jpg" /></p>
<p>
As of August 13 2021, the only way to mitigate this bug is to stop and disable the Print Spooler service.
</p>

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
<td>CVE-2021-36958</td>
<td><a href="https://msrc.microsoft.com/update-guide/vulnerability/CVE-2021-36958">Microsoft</a></td>
</tr>
<tr>
<td>CVE-2021-34481</td>
<td><a href="https://msrc.microsoft.com/update-guide/en-US/vulnerability/CVE-2021-34481">Microsoft</a></td>
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
<td><a href="https://www.vecteezy.com/free-vector/warning">Warning Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://www.vecteezy.com/free-vector/print">Print Vectors by Vecteezy</a></td>
</tr>
<tr>
<td><a href="https://giphy.com/gifs/vintage-computer-old-l1J9qemh1La8b0Rag">8 bit win GIF by Feliks Tomasz Konczakowski via GIPHY</a></td>
</tr>
</tbody>
</table>

<div class="language-python highlighter-rouge"><div class="highlight"><pre class="highlight"><code><span class="c1">## @Author: Sean Bachiller
## @Date: Aug 13 2021
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
