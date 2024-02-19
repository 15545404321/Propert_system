Vue.component('Batupdate', {
	template: `
		<el-dialog title="修改面积" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="建筑面积" prop="fcxx_jzmj">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.fcxx_jzmj" clearable :min="0" placeholder="请输入建筑面积"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="套内面积" prop="fcxx_tnmj">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.fcxx_tnmj" clearable :min="0" placeholder="请输入套内面积"/>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
	`
	,
	components:{
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
			},
			louyu_ids:[],
			fwlx_ids:[],
			member_ids:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Fcxx/getFieldList').then(res => {
					if(res.data.status == 200){
						this.louyu_ids = res.data.data.louyu_ids
						this.fwlx_ids = res.data.data.fwlx_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
		},
		remoteMemberidList(val){
			axios.post(base_url + '/Fcxx/remoteMemberidList',{queryString:val}).then(res => {
				if(res.data.status == 200){
					this.member_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Fcxx/batupdate',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			this.member_ids = []
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
