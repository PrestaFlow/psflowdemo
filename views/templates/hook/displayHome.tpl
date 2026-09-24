{**
 * psflowdemo — home block rendered on the displayHome hook.
 *
 * The title is escaped on output: it is stored as typed in the back office
 * (HTML included), so this template is the only thing standing between an
 * admin-supplied "<script>" and the shop's visitors. See the regression suite
 * tests/prestaflow/Suites/Regression/NoXssInBlockTitle.php.
 *}
<div id="psflowdemo-block" class="psflowdemo-block" data-psflowdemo="home-badge" style="text-align:center;padding:1rem;">
  <h3 class="psflowdemo-block__title">{$psflowdemo_title|escape:'html':'UTF-8'}</h3>
</div>
