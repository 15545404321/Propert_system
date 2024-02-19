Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">开始月份：</td>
						<td>
							{{parseTime(form.scys_ksyf,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结束月份：</td>
						<td>
							{{parseTime(form.scys_jsyf,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">建筑类型：</td>
						<td>
							{{form.jflx_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">建筑类型：</td>
						<td>
							{{form.jflx_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">生成楼宇：</td>
						<td>
							{{form.louyu_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用标准：</td>
						<td>
							{{form.fybz_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">所属物业：</td>
						<td>
							{{form.shop_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">所属小区：</td>
						<td>
							{{form.xqgl_id}}
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
			axios.post(base_url+'/Scys/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
