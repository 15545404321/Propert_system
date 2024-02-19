Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属项目" prop="xqgl_id">
							<el-select   style="width:100%" v-model="form.xqgl_id" filterable clearable placeholder="请选择所属项目">
								<el-option v-for="(item,i) in xqgl_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="结算月份" prop="xz_ffdate">
							<el-date-picker value-format="yyyy-MM" type="month" v-model="form.xz_ffdate" clearable placeholder="请输入结算月份"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="结算周期" prop="gz_zhouqi">
							<el-date-picker value-format="yyyy-MM-dd" type="dates" v-model="form.gz_zhouqi" clearable placeholder="请输入结算周期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="发放金额" prop="gz_jine">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.gz_jine" clearable :min="0" placeholder="请输入发放金额"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="工资明细" prop="gz_mingxi">
							<key-data v-if="show" :item.sync="form.gz_mingxi"></key-data>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="薪资备注" prop="gz_beizhu">
							<el-input  type="textarea" autoComplete="off" v-model="form.gz_beizhu"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入薪资备注"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="发放批次" prop="xzpici_id">
							<el-select   style="width:100%" v-model="form.xzpici_id" filterable clearable placeholder="请选择发放批次">
								<el-option v-for="(item,i) in xzpici_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="生成时间" prop="addtime">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.addtime" clearable placeholder="请输入生成时间"></el-date-picker>
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
	},
	data(){
		return {
			form: {
				shop_id:'',
				xqgl_id:'',
				shop_admin_id:'',
				xz_ffdate:curentTime(),
				gz_zhouqi:'',
				gz_mingxi:[{}],
				gz_beizhu:'',
				xzpici_id:'',
				addtime:curentTime(),
				shop_admin_id:'',
			},
			xqgl_ids:[],
			xzpici_ids:[],
			loading:false,
			rules: {
				gz_jine:[
					{pattern:/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/, message: '发放金额格式错误'}
				],
				xzpici_id:[
					{required: true, message: '发放批次不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Gongzi/getFieldList').then(res => {
					if(res.data.status == 200){
						this.xqgl_ids = res.data.data.xqgl_ids
						this.xzpici_ids = res.data.data.xzpici_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			const myURL = new URL(window.location.href)
			const urlobj = param2Obj(myURL.href)
			this.form.shop_admin_id = urlobj.shop_admin_id
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Gongzi/add',this.form).then(res => {
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
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
			this.form.gz_mingxi = [{}]
		},
	}
})
