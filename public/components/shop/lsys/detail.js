Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">开始时间：</td>
						<td>
							{{parseTime(form.lsys_kstime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结束时间：</td>
						<td>
							{{parseTime(form.lsys_jstime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">建筑类型：</td>
						<td>
							{{form.louyutype_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">选择房间：</td>
						<td>
							{{form.fcxx_fjbh}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">应收金额：</td>
						<td>
							{{form.lsys_ysje}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">备注：</td>
						<td>
							{{form.lsys_bz}}
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
			axios.post(base_url+'/Lsys/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
