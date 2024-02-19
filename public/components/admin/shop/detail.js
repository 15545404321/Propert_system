Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">公司名称：</td>
						<td>
							{{form.shop_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开始日期：</td>
						<td>
							{{parseTime(form.start_date,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">到期日期：</td>
						<td>
							{{parseTime(form.end_date,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">购买功能：</td>
						<td>
							{{form.goumai}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">小区上限：</td>
						<td>
							{{form.restrict_num}}
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
				shoprole:{},
			},
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/Shop/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
