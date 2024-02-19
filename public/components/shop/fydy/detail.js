Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="800px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">费用类型：</td>
						<td>
							{{form.fylx_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">选择类型：</td>
						<td>
							{{form.fylx_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用名称：</td>
						<td>
							{{form.fydy_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用类别：</td>
						<td>
							{{form.fylb_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用单位：</td>
						<td>
							{{form.fydw_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">应收月份：</td>
						<td>
							<span v-if="form.fydy_ysyf == '1'">计费开始日期所在月</span>
							<span v-if="form.fydy_ysyf == '0'">计费结束日期所在月</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">应收日份：</td>
						<td>
							<span v-if="form.fydy_ysr == '1'">指定月末</span>
							<span v-if="form.fydy_ysr == '2'">指定日期</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">指定日期：</td>
						<td>
							{{parseTime(form.fydy_zdr,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">延迟月数：</td>
						<td>
							{{form.fydy_ycys}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">违约金比：</td>
						<td>
							{{form.fydy_wyjbl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">排序：</td>
						<td>
							{{form.fydy_px}}
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
			axios.post(base_url+'/Fydy/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
