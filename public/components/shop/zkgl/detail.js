Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">优惠名称：</td>
						<td>
							{{form.zkgl_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">优惠折扣：</td>
						<td>
							{{form.zkgl_zks}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">操作人员：</td>
						<td>
							{{form.cname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">添加时间：</td>
						<td>
							{{parseTime(form.zkgl_addtime)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">备注信息：</td>
						<td>
							{{form.zkgl_remarks}}
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
			axios.post(base_url+'/Zkgl/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})