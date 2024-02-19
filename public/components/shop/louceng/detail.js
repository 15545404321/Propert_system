Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
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
					<tr>
						<td class="title" width="100">所属楼宇：</td>
						<td>
							{{form.louyu_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">所属楼层：</td>
						<td>
							{{form.louceng_sslc}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">名称后缀：</td>
						<td>
							{{form.loucen_mchz}}
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
			axios.post(base_url+'/LouCeng/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
