Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">票据类型：</td>
						<td>
							{{form.pjlx_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">票据类型：</td>
						<td>
							{{form.pjlx_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">票据模板：</td>
						<td>
							{{form.pjlx_pid}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">票据名称：</td>
						<td>
							{{form.pjgl_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">起始编码：</td>
						<td>
							{{form.pjgl_qsbm}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">票据张数：</td>
						<td>
							{{form.pjgl_pjzs}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">创建人员：</td>
						<td>
							{{form.cname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">录入时间：</td>
						<td>
							{{parseTime(form.pjgl_time,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">启用状态：</td>
						<td>
							<span v-if="form.pjgl_status == '1'">正常</span>
							<span v-if="form.pjgl_status == '0'">禁用</span>
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
			axios.post(base_url+'/Pjgl/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
