<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

?>
<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(current_url()); ?>
    <div class="pagination">
    <?php echo $pagination;?>
    </div>
    <table>
        <tr>
            <th>ID</th>
            <th>Feed Submission Id</th>
            <th>Feed Type</th>
            <th>Submitted Date</th>
            <th>Feed Processing Status</th>
            <th>Started Processing Date</th>
            <th>Completed Processing Date</th>
            <th>Request Result</th>
        </tr>
        <?php if (count($logs)>0):?>
        <?php foreach ($logs as $row):?>
        <tr>
            <td><?php echo $row->id;?></td>
            <td class="feed_id_<?php echo $row->Feed_Submission_Id;?>"><?php echo $row->Feed_Submission_Id;?></td>
            <td><?php echo humanize($row->Feed_Type);?></td>
            <td><?php echo date('Y-m-d H:m:s',strtotime($row->Submitted_Date));?></td>
            <td><?php echo humanize($row->Feed_Processing_Status);?></td>
            <td><?php echo date('Y-m-d H:m:s',strtotime($row->Started_Processing_Date));?></td>
            <td><?php echo date('Y-m-d H:m:s',strtotime($row->Completed_Processing_Date));?></td>
            <td>
                <?php if($row->Request_Result):?>
                    <a href="javascript:void(0)" class="xml_icon" onclick="open_modal_with_content($('#Request_Result_<?php echo $row->id;?>').html());"></a>
                <?php endif;?>
                    <div id="Request_Result_<?php echo $row->id;?>" style="display:none;">
                        <pre>
<?php echo htmlentities($row->Request_Result);?>
                        </pre>
                    </div>
            </td>
        </tr>
        <?php endforeach;?>
        <?php endif;?>
    </table>
    <div class="pagination">
    <?php echo $pagination;?>
    </div>
    </form>
</article>