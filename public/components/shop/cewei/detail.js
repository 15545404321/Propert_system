Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">车位编号：</td>
						<td>
							{{form.cewei_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">车位类型：</td>
						<td>
							{{form.cwlx_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">停车场地：</td>
						<td>
							{{form.tccd_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">车位区域：</td>
						<td>
							{{form.cwqy_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">车位状态：</td>
						<td>
							{{form.cwzt_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">车位面积：</td>
						<td>
							{{form.cewei_cwmj}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开始日期：</td>
						<td>
							{{parseTime(form.cewei_start_time,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结束日期：</td>
						<td>
							{{parseTime(form.cewei_end_time,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">资产编号：</td>
						<td>
							{{form.cewei_zcbh}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">产权所属：</td>
						<td>
							{{form.member_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">备注信息：</td>
						<td>
							{{form.cewei_remarks}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	`
	,
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		info: {
			type: Object,
		},
	},
	data() {
		return {
			form:{
			},
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/Cewei/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
